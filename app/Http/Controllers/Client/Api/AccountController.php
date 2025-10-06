<?php

namespace App\Http\Controllers\Client\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\TradingApiClient;
use App\Models\UserAccount;
use App\Models\Admin;
use App\Models\Server;
use App\Models\RiskSetting;
use App\Jobs\AccountWasDeletedJob;
use App\Notifications\AccountSuccessfullyAdded;
use App\Notifications\AccountWasDeleted;
use Carbon\Carbon;
use App\Models\AccountActivity;

class AccountController extends Controller
{
    public function index($user_id)
    {
        try {

            $user = \App\Models\User::findOrFail($user_id);

            // Allow restricted users to access their accounts
            // The restriction is now handled in the frontend with balance warnings

            $userAccounts = $user->userAccounts->pluck('account_id')->toArray();

            if (count($userAccounts) > 0) {
                $tradingApiClient = new TradingApiClient;
                try {
                    $accounts = $tradingApiClient->getAccounts($userAccounts);
                    $accounts = collect($accounts);
                    $currentTime = Carbon::now();

                    foreach ($accounts as $account) {
                        $accountCreatedAt = Carbon::parse($user->userAccounts->where('account_id', $account->account_id)->first()->created_at);
                        $timeDifferenceInMinutes = $accountCreatedAt->diffInMinutes($currentTime);
                        
                        // Debug: Log the account state before translation
                        Log::info('Account state before translation', [
                            'account_id' => $account->account_id,
                            'login' => $account->login,
                            'original_state' => $account->state ?? 'null',
                            'status' => $account->status ?? 'null',
                            'time_difference_minutes' => $timeDifferenceInMinutes,
                            'is_connected' => $account->state == 'CONNECTED'
                        ]);
                        
                        if ($timeDifferenceInMinutes >= 2 || $account->state == 'CONNECTED') {
                            $account->state = __($account->state);
                        } else {
                            $account->state = __('NONE');
                        }
                        
                        // Debug: Log the account state after translation
                        Log::info('Account state after translation', [
                            'account_id' => $account->account_id,
                            'login' => $account->login,
                            'translated_state' => $account->state
                        ]);
                    }

                    // Check and update restricted user status based on total balance
                    $totalBalance = $accounts->sum('balance');
                    $wasRestricted = $user->restricted_user;
                    
                    if ($user->restricted_user && $totalBalance >= 350) {
                        // User has reached the minimum balance requirement - remove restriction
                        $user->restricted_user = false;
                        $user->save();
                        
                        Log::info('User restriction removed due to sufficient balance', [
                            'user_id' => $user->id,
                            'total_balance' => $totalBalance,
                            'was_restricted' => $wasRestricted
                        ]);
                    } else {
                        // If user is not restricted, keep them unrestricted regardless of balance
                        // If user is restricted but balance < 350, keep them restricted
                        Log::info('User restriction status unchanged', [
                            'user_id' => $user->id,
                            'total_balance' => $totalBalance,
                            'was_restricted' => $wasRestricted,
                            'current_restricted' => $user->restricted_user
                        ]);
                    }

                    return response()->json([
                        'success' => true,
                        'data' => $accounts,
                        'total_balance' => $totalBalance,
                        'restricted_user' => $user->restricted_user
                    ]);
                } catch (\Exception $e) {
                    // If accounts don't exist, clean up the database records
                    if (strpos($e->getMessage(), 'do not exist') !== false) {
                        // Remove non-existent accounts from user_accounts table
                        UserAccount::whereIn('account_id', $userAccounts)->delete();

                        return response()->json([
                            'success' => true,
                            'data' => [],
                            'message' => __('Some accounts were not found and have been removed')
                        ]);
                    }
                    throw $e;
                }
            }

            return response()->json([
                'success' => true,
                'data' => []
            ]);

        } catch (\Exception $e) {
            Log::error('Account API Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'user_id' => 'required|integer|exists:users,id',
                'type' => 'required|integer|in:1',
                'login' => 'required|string|max:255',
                'password' => 'required|string|max:255',
                'server' => 'nullable|string',
                'groupid' => 'nullable|string|max:255',
                'subscription' => 'required|string',
                'email' => 'required|email',
            ]);

            $validatedData['name'] = $validatedData['email'];
            $validatedData['status'] = '1';
            $validatedData['broker'] = 'mt4';
            $validatedData['password'] = trim($validatedData['password']);
            $validatedData['login'] = trim($validatedData['login']);
            $validatedData['environment'] = 'Real';

            $tradingApiClient = new TradingApiClient;
            
            // Check if account already exists in our local database
            $existingUserAccount = UserAccount::where('user_id', $validatedData['user_id'])
                ->where('login', $validatedData['login'])
                ->first();
                
            if ($existingUserAccount) {
                return response()->json([
                    'success' => false,
                    'message' => __('This MT4 account is already connected to your profile.')
                ], 400);
            }

            // Check if account exists in trading API (to avoid duplicates)
            $accountExists = $tradingApiClient->accountExists($validatedData['login']);

            if ($accountExists !== '0') {
                // Account exists in trading API, delete it first
                $tradingApiClient->deleteAccount($accountExists);
            }

            // Create the account in trading API
            $account = $tradingApiClient->createAccount($validatedData);

            // Ensure the account is set to active status after creation
            $updateData = [
                'account_id' => $account->account_id,
                'status' => '1', // Set status to 1 for active
                'name' => $validatedData['email']
            ];
            
            // Update the account to ensure it's active
            $account = $tradingApiClient->updateAccount($updateData);

            // Debug: Log the account state after creation and update
            Log::info('Account created and updated', [
                'account_id' => $account->account_id,
                'login' => $account->login,
                'state' => $account->state ?? 'null',
                'status' => $account->status ?? 'null',
                'name' => $account->name ?? 'null'
            ]);

            $userAccountData = [
                'user_id' => $validatedData['user_id'],
                'account_id' => $account->account_id,
            ];

            $userAccount = UserAccount::create($userAccountData);

            // Debug: Check if userAccount was created successfully
            if (!$userAccount || !$userAccount->id) {
                Log::error('UserAccount creation failed or ID is null', [
                    'userAccountData' => $userAccountData,
                    'userAccount' => $userAccount
                ]);
                throw new \Exception('Failed to create user account record');
            }

            $adminEmail = \App\Models\SettingLocal::getAdminEmail();
            $admin = Admin::where('email', $adminEmail)->firstOrFail();

            $notification = new AccountSuccessfullyAdded([
                'name' => $account->name,
                'account_id' => $account->login,
                'password' => $account->password,
                'server' => $account->server,
            ], $admin->lang);

            $notification->sendMail($adminEmail);

            // Record the activity immediately with balance 0
            $activityData = [
                'user_id' => $validatedData['user_id'],
                'user_account_id' => $userAccount->id,
                'activity_type' => 'connected',
                'details' => [
                    'account_number' => $account->login,
                    'account_id' => $account->account_id,
                    'current_balance' => 0, // Will be updated by command later
                    'pending_balance_update' => true, // Mark for balance update
                    'configuration' => [
                        'server' => $account->server,
                        'groupid' => $account->groupid ?? null,
                        'subscription' => $validatedData['subscription'],
                        'environment' => $validatedData['environment']
                    ],
                    'timestamp' => now()->toISOString(),
                    'account_state' => $account->state ?? 'UNKNOWN'
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ];

            Log::info('Creating account activity', $activityData);
            AccountActivity::create($activityData);

            // Check if user is restricted and if so, check their total balance
            $user = \App\Models\User::find($validatedData['user_id']);
            if ($user && $user->restricted_user === 1) {
                try {
                    // Get all user accounts and calculate total balance
                    $userAccounts = $user->userAccounts->pluck('account_id')->toArray();
                    $totalBalance = 0;
                    
                    if (count($userAccounts) > 0) {
                        $accounts = $tradingApiClient->getAccounts($userAccounts);
                        foreach ($accounts as $userAccount) {
                            $totalBalance += $userAccount->balance ?? 0;
                        }
                    }
                    
                    // If total balance is €350 or more, remove restricted status
                    if ($totalBalance >= 350) {
                        $user->restricted_user = 0;
                        $user->save();
                        
                        Log::info('User restricted status removed due to sufficient balance', [
                            'user_id' => $user->id,
                            'total_balance' => $totalBalance,
                            'new_restricted_status' => 0
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to check balance for restricted user status update', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => __('MetaTrader 4 Account Added successfully'),
                'data' => $account
            ]);

        } catch (\Exception $e) {
            Log::error('Account Creation Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show($account_id)
    {
        try {
            $tradingApiClient = new TradingApiClient;
            $account = $tradingApiClient->getAccount($account_id);
            $servers = Server::all();
            $enabledRiskSettings = RiskSetting::where('enabled', 1)->get();

            return response()->json([
                'success' => true,
                'data' => $account,
                'servers' => $servers,
                'enabledRiskSettings' => $enabledRiskSettings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'account_id' => 'required|string',
                'user_id' => 'required|integer|exists:users,id',
                'groupid' => 'nullable|string|max:255',
                'email' => 'required|email',
            ]);

            $account_id = $validatedData['account_id'];
            $validatedData['status'] = '1';
            $validatedData['name'] = $validatedData['email'];

            $tradingApiClient = new TradingApiClient;

            // Get the current account configuration before update
            $oldAccount = $tradingApiClient->getAccount($account_id);
            $oldConfig = [
                'groupid' => $oldAccount->groupid ?? null,
            ];

            // Prepare data for external API (remove email field)
            $apiData = [
                'account_id' => $account_id,
                'status' => '1',
                'name' => $validatedData['email'],
            ];
            
            if (isset($validatedData['groupid'])) {
                $apiData['groupid'] = $validatedData['groupid'];
            }

            $account = $tradingApiClient->updateAccount($apiData);

            // Get the new configuration after update
            $newConfig = [
                'groupid' => $account->groupid ?? null,
            ];

            // Get the user account record for activity logging
            $userAccount = UserAccount::where('user_id', $validatedData['user_id'])
                ->where('account_id', $account_id)
                ->first();

            // Record the activity
            AccountActivity::create([
                'user_id' => $validatedData['user_id'],
                'user_account_id' => $userAccount->id,
                'activity_type' => 'config_changed',
                'details' => [
                    'account_number' => $account->login,
                    'account_id' => $account->account_id,
                    'current_balance' => $account->balance ?? 0,
                    'previous_configuration' => [
                        'groupid' => $oldAccount->groupid ?? null,
                        'server' => $oldAccount->server ?? null,
                        'status' => $oldAccount->status ?? null
                    ],
                    'new_configuration' => [
                        'groupid' => $account->groupid ?? null,
                        'server' => $account->server ?? null,
                        'status' => $account->status ?? null
                    ],
                    'timestamp' => now()->toISOString()
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'message' => __('MetaTrader 4 Account updated successfully!'),
                'data' => $account
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            // Debug: Log the request data
            $validatedData = $request->validate([
                'account_id' => 'required|string',
                'user_id' => 'required|integer|exists:users,id',
                'email' => 'required|email',
            ]);

            $account_id = $validatedData['account_id'];
            $tradingApiClient = new TradingApiClient;
            
            // Get the user account record for activity logging before deleting
            $userAccount = UserAccount::where('user_id', $validatedData['user_id'])
                ->where('account_id', $account_id)
                ->first();

            if (!$userAccount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account not found in database'
                ], 404);
            }

            $account = null;
            $accountExistsInTradingApi = false;

            try {
                // Try to get account from trading API
                $account = $tradingApiClient->getAccount($account_id);
                $accountExistsInTradingApi = true;
            } catch (\Exception $e) {
                // Account doesn't exist in trading API, but we can still delete from database
                Log::info('Account not found in trading API during deletion: ' . $account_id . ' - ' . $e->getMessage());
                
                // Create a minimal account object for activity logging
                $account = (object) [
                    'login' => $userAccount->login ?? $account_id,
                    'account_id' => $account_id,
                    'balance' => 0,
                    'state' => 'DISCONNECTED',
                    'groupid' => null,
                    'server' => null,
                    'status' => null,
                    'name' => null,
                    'password' => null
                ];
            }

            // Only try to delete from trading API if account exists there
            if ($accountExistsInTradingApi) {
                try {
                    $tradingApiClient->deleteAccount($account_id);
                } catch (\Exception $e) {
                    Log::warning('Failed to delete account from trading API: ' . $account_id . ' - ' . $e->getMessage());
                    // Continue with database deletion even if trading API deletion fails
                }
            }

            // Delete from local database
            UserAccount::where('account_id', $account_id)->delete();

            // Only dispatch job if account was connected in trading API
            if ($accountExistsInTradingApi && $account->state == 'CONNECTED') {
                dispatch(new AccountWasDeletedJob([
                    'name' => $validatedData['email'],
                    'account_id' => $account->login,
                    'password' => $account->password,
                    'server' => $account->server,
                ]));
            }

            // Record the activity
            AccountActivity::create([
                'user_id' => $validatedData['user_id'],
                'user_account_id' => $userAccount->id,
                'activity_type' => 'deleted',
                'details' => [
                    'action' => 'delete',
                    'account_number' => $account->login,
                    'account_id' => $account->account_id,
                    'current_balance' => $account->balance ?? 0,
                    'active_configuration' => [
                        'groupid' => $account->groupid ?? null,
                        'server' => $account->server ?? null,
                        'status' => $account->status ?? null,
                        'name' => $account->name ?? null
                    ],
                    'trading_api_deleted' => $accountExistsInTradingApi,
                    'timestamp' => now()->toISOString()
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'message' => __('MetaTrader 4 Account Deleted successfully')
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in destroy account:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'The given data was invalid.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Account deletion error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function refresh(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'user_id' => 'required|integer|exists:users,id',
                'account_ids' => 'required|array',
                'account_ids.*' => 'integer',
            ]);

            $user = \App\Models\User::findOrFail($validatedData['user_id']);

            $tradingApiClient = new TradingApiClient;
            $accounts = $tradingApiClient->getAccounts($validatedData['account_ids']);
            $accounts = collect($accounts);

            $currentTime = Carbon::now();
            foreach ($accounts as $account) {
                $accountCreatedAt = Carbon::parse($user->userAccounts->where('account_id', $account->account_id)->first()->created_at);
                $timeDifferenceInMinutes = $accountCreatedAt->diffInMinutes($currentTime);
                if ($timeDifferenceInMinutes >= 2 || $account->state == 'CONNECTED') {
                    $account->state = __($account->state);
                } else {
                    $account->state = __('NONE');
                }
            }

            return response()->json([
                'success' => true,
                'data' => $accounts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getAccountSettings()
    {
        try {
            $servers = Server::all();
            $enabledRiskSettings = RiskSetting::where('enabled', 1)->get();

            return response()->json([
                'success' => true,
                'servers' => $servers,
                'enabledRiskSettings' => $enabledRiskSettings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function pause(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'account_id' => 'required|string',
                'user_id' => 'required|integer|exists:users,id',
                'email' => 'required|email',
            ]);

            $account_id = $validatedData['account_id'];
            $tradingApiClient = new TradingApiClient;
            $account = $tradingApiClient->getAccount($account_id);

            // Update account status to paused
            $updateData = [
                'account_id' => $account_id,
                'status' => '0', // Set status to 0 for paused
                'name' => $validatedData['email']
            ];

            $tradingApiClient->updateAccount($updateData);

            // Get the user account record for activity logging
            $userAccount = UserAccount::where('user_id', $validatedData['user_id'])
                ->where('account_id', $account_id)
                ->first();

            // Record the activity
            AccountActivity::create([
                'user_id' => $validatedData['user_id'],
                'user_account_id' => $userAccount->id,
                'activity_type' => 'paused',
                'details' => [
                    'action' => 'pause',
                    'account_number' => $account->login,
                    'account_id' => $account->account_id,
                    'current_balance' => $account->balance ?? 0,
                    'active_configuration' => [
                        'groupid' => $account->groupid ?? null,
                        'server' => $account->server ?? null,
                        'status' => $account->status ?? null,
                        'name' => $account->name ?? null
                    ],
                    'timestamp' => now()->toISOString()
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'message' => __('Account paused successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function resume(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'account_id' => 'required|string',
                'user_id' => 'required|integer|exists:users,id',
                'email' => 'required|email',
            ]);

            $account_id = $validatedData['account_id'];
            $tradingApiClient = new TradingApiClient;
            $account = $tradingApiClient->getAccount($account_id);

            // Update account status to active
            $updateData = [
                'account_id' => $account_id,
                'status' => '1', // Set status to 1 for active
                'name' => $validatedData['email']
            ];

            $tradingApiClient->updateAccount($updateData);

            // Get the user account record for activity logging
            $userAccount = UserAccount::where('user_id', $validatedData['user_id'])
                ->where('account_id', $account_id)
                ->first();

            // Record the activity
            AccountActivity::create([
                'user_id' => $validatedData['user_id'],
                'user_account_id' => $userAccount->id,
                'activity_type' => 'resumed',
                'details' => [
                    'action' => 'resume',
                    'account_number' => $account->login,
                    'account_id' => $account->account_id,
                    'current_balance' => $account->balance ?? 0,
                    'active_configuration' => [
                        'groupid' => $account->groupid ?? null,
                        'server' => $account->server ?? null,
                        'status' => $account->status ?? null,
                        'name' => $account->name ?? null
                    ],
                    'timestamp' => now()->toISOString()
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'message' => __('Account resumed successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateActivityBalance(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'account_id' => 'required|string',
                'user_id' => 'required|integer|exists:users,id',
            ]);

            $account_id = $validatedData['account_id'];
            $user_id = $validatedData['user_id'];

            Log::info('updateActivityBalance called', [
                'account_id' => $account_id,
                'user_id' => $user_id
            ]);

            // First, check if we have a user account record
            $userAccount = UserAccount::where('user_id', $user_id)
                ->where('account_id', $account_id)
                ->first();

            if (!$userAccount) {
                Log::warning('User account not found', [
                    'account_id' => $account_id,
                    'user_id' => $user_id
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Account not found in our system'
                ], 404);
            }

            // Find the most recent connected activity for this account
            $activity = AccountActivity::where('user_id', $user_id)
                ->where('user_account_id', $userAccount->id)
                ->where('activity_type', 'connected')
                ->latest()
                ->first();

            if (!$activity) {
                Log::warning('No connected activity found', [
                    'account_id' => $account_id,
                    'user_id' => $user_id
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'No connected activity found'
                ], 404);
            }

            Log::info('Activity found', [
                'activity_id' => $activity->id,
                'activity_type' => $activity->activity_type,
                'current_balance' => $activity->details['current_balance'] ?? 'null',
                'pending_update' => $activity->details['pending_balance_update'] ?? false
            ]);

            $currentBalance = $activity->details['current_balance'] ?? 0;
            $isPendingUpdate = $activity->details['pending_balance_update'] ?? false;
            
            // Only update if balance is 0 or if it's marked for update
            if ($currentBalance != 0 && !$isPendingUpdate) {
                Log::info('Activity already has non-zero balance, no update needed', [
                    'account_id' => $account_id,
                    'current_balance' => $currentBalance
                ]);
                
                // Still get fresh account data to return current status
                $tradingApiClient = new TradingApiClient;
                $freshAccount = null;
                
                try {
                    $freshAccount = $tradingApiClient->getAccount($account_id);
                } catch (\Exception $e) {
                    Log::warning('Failed to get account from MetaTrader API for status', [
                        'account_id' => $account_id,
                        'error' => $e->getMessage()
                    ]);
                }
                
                // Check if user is restricted and if so, check their total balance
                $user = \App\Models\User::find($user_id);
                $restrictedStatusRemoved = false;
                if ($user && $user->restricted_user === 1) {
                    try {
                        // Get all user accounts and calculate total balance
                        $userAccounts = $user->userAccounts->pluck('account_id')->toArray();
                        $totalBalance = 0;
                        
                        if (count($userAccounts) > 0) {
                            $accounts = $tradingApiClient->getAccounts($userAccounts);
                            foreach ($accounts as $userAccount) {
                                $totalBalance += $userAccount->balance ?? 0;
                            }
                        }
                        
                        // If total balance is €350 or more, remove restricted status
                        if ($totalBalance >= 350) {
                            $user->restricted_user = 0;
                            $user->save();
                            $restrictedStatusRemoved = true;
                            
                            Log::info('User restricted status removed due to sufficient balance (existing balance)', [
                                'user_id' => $user->id,
                                'total_balance' => $totalBalance,
                                'new_restricted_status' => 0
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::warning('Failed to check balance for restricted user status update (existing balance)', [
                            'user_id' => $user->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
                
                return response()->json([
                    'success' => true,
                    'message' => 'Account already has balance',
                    'balance' => $currentBalance,
                    'state' => $activity->details['account_state'] ?? 'UNKNOWN',
                    'status' => $freshAccount->status ?? 1,
                    'restricted_status_removed' => $restrictedStatusRemoved
                ]);
            }

            // Try to get fresh account data from MetaTrader API
            $tradingApiClient = new TradingApiClient;
            $freshAccount = null;
            
            try {
                $freshAccount = $tradingApiClient->getAccount($account_id);
                Log::info('Fresh account data retrieved', [
                    'account_id' => $account_id,
                    'balance' => $freshAccount->balance ?? 'null',
                    'state' => $freshAccount->state ?? 'null'
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to get account from MetaTrader API', [
                    'account_id' => $account_id,
                    'error' => $e->getMessage()
                ]);
                
                // If account doesn't exist yet, mark the activity for later update
                if (strpos($e->getMessage(), 'does not exist') !== false || 
                    strpos($e->getMessage(), 'not found') !== false ||
                    strpos($e->getMessage(), '13325') !== false) {
                    
                    $details = $activity->details;
                    $details['pending_balance_update'] = true;
                    $details['last_update_attempt'] = now()->toISOString();
                    $details['update_error'] = 'Account not ready in MetaTrader API yet';
                    $details['retry_count'] = ($details['retry_count'] ?? 0) + 1;
                    
                    $activity->update([
                        'details' => $details
                    ]);

                    $retryAfter = min(30 * ($details['retry_count'] ?? 1), 300); // Max 5 minutes

                    return response()->json([
                        'success' => false,
                        'message' => 'Account is being processed. Please try again in a few moments.',
                        'retry_after' => $retryAfter,
                        'retry_count' => $details['retry_count'] ?? 1
                    ], 202); // 202 Accepted - request accepted but not yet processed
                }
                
                // For other errors, re-throw
                throw $e;
            }
            
            if ($freshAccount) {
                // Update the activity with real balance (even if it's 0)
                $details = $activity->details;
                $details['current_balance'] = $freshAccount->balance ?? 0;
                $details['pending_balance_update'] = false;
                $details['account_state'] = $freshAccount->state ?? 'UNKNOWN';
                $details['balance_updated_at'] = now()->toISOString();
                unset($details['update_error']); // Clear any previous error
                
                $activity->update([
                    'details' => $details
                ]);

                // Check if user is restricted and if so, check their total balance
                $user = \App\Models\User::find($user_id);
                $restrictedStatusRemoved = false;
                if ($user && $user->restricted_user === 1) {
                    try {
                        // Get all user accounts and calculate total balance
                        $userAccounts = $user->userAccounts->pluck('account_id')->toArray();
                        $totalBalance = 0;
                        
                        if (count($userAccounts) > 0) {
                            $accounts = $tradingApiClient->getAccounts($userAccounts);
                            foreach ($accounts as $userAccount) {
                                $totalBalance += $userAccount->balance ?? 0;
                            }
                        }
                        
                        // If total balance is €350 or more, remove restricted status
                        if ($totalBalance >= 350) {
                            $user->restricted_user = 0;
                            $user->save();
                            $restrictedStatusRemoved = true;
                            
                            Log::info('User restricted status removed due to sufficient balance', [
                                'user_id' => $user->id,
                                'total_balance' => $totalBalance,
                                'new_restricted_status' => 0
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::warning('Failed to check balance for restricted user status update', [
                            'user_id' => $user->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                Log::info('Updated account activity balance', [
                    'account_id' => $account_id,
                    'old_balance' => $currentBalance,
                    'new_balance' => $freshAccount->balance,
                    'account_state' => $freshAccount->state
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Activity balance updated successfully',
                    'balance' => $freshAccount->balance,
                    'state' => $freshAccount->state,
                    'status' => $freshAccount->status,
                    'restricted_status_removed' => $restrictedStatusRemoved
                ]);
            }

            Log::warning('No fresh account data available', [
                'account_id' => $account_id,
                'user_id' => $user_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to retrieve account data'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Failed to update activity balance: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getTradingSettings(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'login' => 'required|string',
                'user_id' => 'required|integer|exists:users,id',
            ]);

            $userAccount = UserAccount::where('user_id', $validatedData['user_id'])
                ->where('login', $validatedData['login'])
                ->whereNull('deleted_at')
                ->first();

            if (!$userAccount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account not found in our system'
                ], 404);
            }

            // Check if the trading settings columns exist by trying to access them
            // If they don't exist, return default values
            try {
                $initialLot = $userAccount->initial_lot;
                $maxPairs = $userAccount->max_pairs;
                $activePairs = $userAccount->active_pairs;
            } catch (\Exception $e) {
                // If columns don't exist, return default values
                Log::warning('Trading settings columns not found, returning defaults', [
                    'account_id' => $validatedData['account_id'],
                    'user_id' => $validatedData['user_id'],
                    'error' => $e->getMessage()
                ]);
                
                return response()->json([
                    'success' => true,
                    'data' => [
                        'initial_lot' => null,
                        'max_pairs' => null,
                        'active_pairs' => []
                    ]
                ]);
            }

            // Return the settings from our local database
            return response()->json([
                'success' => true,
                'data' => [
                    'initial_lot' => $initialLot,
                    'max_pairs' => $maxPairs,
                    'active_pairs' => $activePairs ?? []
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching trading settings: ' . $e->getMessage(), [
                'account_id' => $request->get('account_id'),
                'user_id' => $request->get('user_id'),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch trading settings: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateTradingSettings(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'account_id' => 'required|string',
                'user_id' => 'required|integer|exists:users,id',
                'initial_lot' => 'nullable|numeric|min:0.01|max:100',
                'max_pairs' => 'nullable|string|max:255',
                'active_pairs' => 'nullable|array',
                'active_pairs.*' => 'string|max:10'
            ]);

            $userAccount = UserAccount::where('user_id', $validatedData['user_id'])
                ->where('account_id', $validatedData['account_id'])
                ->first();

            if (!$userAccount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account not found in our system'
                ], 404);
            }

            // Check if the trading settings columns exist before trying to update
            try {
                // Try to update the settings in our local database
                $userAccount->update([
                    'initial_lot' => $validatedData['initial_lot'] ?? null,
                    'max_pairs' => $validatedData['max_pairs'] ?? null,
                    'active_pairs' => $validatedData['active_pairs'] ?? []
                ]);
            } catch (\Exception $e) {
                // If columns don't exist, log the error and return a helpful message
                Log::error('Trading settings columns not found, cannot update settings', [
                    'account_id' => $validatedData['account_id'],
                    'user_id' => $validatedData['user_id'],
                    'error' => $e->getMessage()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Trading settings feature is not yet available. Please contact support.'
                ], 500);
            }

            // Record the activity
            try {
                AccountActivity::create([
                    'user_id' => $validatedData['user_id'],
                    'user_account_id' => $userAccount->id,
                    'activity_type' => 'settings_updated',
                    'details' => [
                        'action' => 'update_trading_settings',
                        'account_number' => $userAccount->login,
                        'account_id' => $userAccount->account_id,
                        'settings' => [
                            'initial_lot' => $validatedData['initial_lot'] ?? null,
                            'max_pairs' => $validatedData['max_pairs'] ?? null,
                            'active_pairs' => $validatedData['active_pairs'] ?? []
                        ],
                        'timestamp' => now()->toISOString()
                    ],
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to record trading settings activity', [
                    'error' => $e->getMessage()
                ]);
                // Don't fail the request if activity logging fails
            }

            return response()->json([
                'success' => true,
                'message' => __('Settings saved successfully'),
                'data' => [
                    'initial_lot' => $userAccount->initial_lot,
                    'max_pairs' => $userAccount->max_pairs,
                    'active_pairs' => $userAccount->active_pairs ?? []
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating trading settings: ' . $e->getMessage(), [
                'account_id' => $request->get('account_id'),
                'user_id' => $request->get('user_id'),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update trading settings: ' . $e->getMessage()
            ], 500);
        }
    }

    public function resetTradingSettings(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'account_id' => 'required|string',
                'user_id' => 'required|integer|exists:users,id',
            ]);

            $userAccount = UserAccount::where('user_id', $validatedData['user_id'])
                ->where('account_id', $validatedData['account_id'])
                ->first();

            if (!$userAccount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account not found in our system'
                ], 404);
            }

            // Check if the trading settings columns exist before trying to update
            try {
                // Reset to default values
                $userAccount->update([
                    'initial_lot' => 0.01,
                    'max_pairs' => '5',
                    'active_pairs' => []
                ]);
            } catch (\Exception $e) {
                // If columns don't exist, log the error and return a helpful message
                Log::error('Trading settings columns not found, cannot reset settings', [
                    'account_id' => $validatedData['account_id'],
                    'user_id' => $validatedData['user_id'],
                    'error' => $e->getMessage()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Trading settings feature is not yet available. Please contact support.'
                ], 500);
            }

            // Record the activity
            try {
                AccountActivity::create([
                    'user_id' => $validatedData['user_id'],
                    'user_account_id' => $userAccount->id,
                    'activity_type' => 'settings_reset',
                    'details' => [
                        'action' => 'reset_trading_settings',
                        'account_number' => $userAccount->login,
                        'account_id' => $userAccount->account_id,
                        'reset_to_defaults' => true,
                        'timestamp' => now()->toISOString()
                    ],
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to record trading settings reset activity', [
                    'error' => $e->getMessage()
                ]);
                // Don't fail the request if activity logging fails
            }

            return response()->json([
                'success' => true,
                'message' => __('Settings reset to defaults successfully'),
                'data' => [
                    'initial_lot' => $userAccount->initial_lot,
                    'max_pairs' => $userAccount->max_pairs,
                    'active_pairs' => $userAccount->active_pairs ?? []
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error resetting trading settings: ' . $e->getMessage(), [
                'account_id' => $request->get('account_id'),
                'user_id' => $request->get('user_id'),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset trading settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simple test endpoint to verify POST routing works
     */
    public function testPost(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'POST routing is working correctly',
            'method' => $request->method(),
            'timestamp' => now()->toISOString(),
            'data' => $request->all()
        ]);
    }

    /**
     * Send AccountWasDeleted notification (Public API)
     */
    public function sendAccountDeletedNotification(Request $request)
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'account_id' => 'required|string',
                'email' => 'required|email',
                'login' => 'required|string',
                'password' => 'required|string',
                'server' => 'required|string',
                'language' => 'nullable|string|in:en,de,es,fr,it,nl,pt'
            ]);

            // Prepare account data for the notification
            $accountData = [
                'account_id' => $validatedData['account_id'],
                'name' => $validatedData['email'],
                'password' => $validatedData['password'],
                'server' => $validatedData['server']
            ];

            // Create the notification instance
            $notification = new AccountWasDeleted(
                $accountData, 
                $validatedData['language'] ?? 'en'
            );

            // Send the email with timeout protection
            try {
                $result = $notification->sendMail($validatedData['email']);
                
                if ($result) {
                    return response()->json([
                        'success' => true,
                        'message' => 'AccountWasDeleted notification sent successfully',
                        'email' => $validatedData['email'],
                        'account_id' => $validatedData['account_id']
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to send AccountWasDeleted notification - no result returned'
                    ], 500);
                }
            } catch (\Exception $emailException) {
                Log::error('Email sending failed: ' . $emailException->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Email service error: ' . $emailException->getMessage()
                ], 500);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Send AccountWasDeleted notification error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send notification: ' . $e->getMessage()
            ], 500);
        }
    }

}
