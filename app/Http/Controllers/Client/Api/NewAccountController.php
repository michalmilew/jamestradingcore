<?php

namespace App\Http\Controllers\Client\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\NewTradingApiClient;
use App\Models\UserAccount;
use App\Models\Admin;
use App\Models\Server;
use App\Models\RiskSetting;
use App\Jobs\AccountWasDeletedJob;
use App\Notifications\AccountSuccessfullyAdded;
use App\Notifications\AccountWasDeleted;
use Carbon\Carbon;
use App\Models\AccountActivity;
use Illuminate\Support\Facades\Http;

class NewAccountController extends Controller
{
    /**
     * Public API - Get MetaTrader API server status
     */
    public function getApiStatus()
    {
        try {
            // Reduce timeout to 5 seconds to avoid nginx timeout
            $response = Http::timeout(30)
                ->retry(2, 1000) // Retry 2 times with 1 second delay
                ->get('https://api.jamestradinggroup.com/api/status');

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'data' => $response->json()
                ]);
            }

            Log::error('NewAccountController - getApiStatus failed', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get API status',
                'error' => $response->body()
            ], 500);

        } catch (\Exception $e) {
            Log::error('NewAccountController - getApiStatus exception', [
                'error' => $e->getMessage()
            ]);

            // Return a more graceful error response
            return response()->json([
                'success' => false,
                'message' => 'MetaTrader API server is currently unavailable',
                'error' => 'Connection timeout or server unreachable',
                'timestamp' => now()->toISOString()
            ], 503); // 503 Service Unavailable
        }
    }

    public function index($user_id)
    {
        try {
            // Step 1: Add basic caching to prevent duplicate requests
            $cacheKey = "user_account_request_{$user_id}";
            
            // Check if we have a recent cached result (within 5 seconds)
            if (cache()->has($cacheKey)) {
                $cachedResult = cache()->get($cacheKey);
                $cacheTime = $cachedResult['cache_time'] ?? 0;
                
                if ((time() - $cacheTime) < 5) {
                    Log::info('Returning recent cached result', [
                        'user_id' => $user_id,
                        'cache_age' => time() - $cacheTime
                    ]);
                    return response()->json($cachedResult['data']);
                }
            }
            
            // Step 2: Check if user exists (minimal database check)
            $user = \App\Models\User::findOrFail($user_id);
            
            // Step 2: Interact with NewTradingApiClient first (this is the priority)
            $tradingApiClient = new NewTradingApiClient;
            
            try {
                // Get the specific account for this user from the NewTradingApiClient
                $account = $tradingApiClient->getAccount($user_id);
                
                if ($account == null) {
                    $result = [
                        'success' => false,
                        'data' => null,
                        'message' => 'Account not found in MetaTrader API'
                    ];
                    
                    // Cache the result for 10 seconds to prevent repeated failed requests
                    cache()->put($cacheKey, [
                        'data' => $result,
                        'cache_time' => time()
                    ], 10);
                    
                    return response()->json($result);
                }
                
                Log::info('Account state after translation', [
                    'account_id' => $account->account_id,
                    'login' => $account->login,
                    'translated_state' => $account->state
                ]);
                
                $totalBalance = $account->balance ?? 0;
                $wasRestricted = $user->restricted_user;
                
                // Step 4: Update user restriction status (database operation that can fail)
                try {
                    if ($user->restricted_user && $totalBalance >= 350) {
                        $user->restricted_user = false;
                        $user->save();
                        Log::info('User restriction removed due to sufficient balance', [
                            'user_id' => $user->id,
                            'total_balance' => $totalBalance,
                            'was_restricted' => $wasRestricted
                        ]);
                    } else {
                        Log::info('User restriction status unchanged', [
                            'user_id' => $user->id,
                            'total_balance' => $totalBalance,
                            'was_restricted' => $wasRestricted,
                            'current_restricted' => $user->restricted_user
                        ]);
                    }
                } catch (\Exception $e) {
                    // Log database errors but don't fail the request
                    Log::error('Failed to update user restriction status', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                }
                
                // Step 5: Prepare and cache the response
                $result = [
                    'success' => true,
                    'data' => $account,
                    'total_balance' => $totalBalance,
                    'restricted_user' => $user->restricted_user
                ];
                
                // Cache successful result for 5 seconds to handle concurrent requests
                cache()->put($cacheKey, [
                    'data' => $result,
                    'cache_time' => time()
                ], 5);
                
                return response()->json($result);
                
            } catch (\Exception $e) {
                Log::error('NewTradingApiClient failed in index method', [
                    'user_id' => $user_id,
                    'error' => $e->getMessage()
                ]);
                
                // Cache error result for 10 seconds to prevent repeated failed requests
                $errorResult = [
                    'success' => false,
                    'message' => 'Failed to get account from MetaTrader API: ' . $e->getMessage()
                ];
                
                cache()->put($cacheKey, [
                    'data' => $errorResult,
                    'cache_time' => time()
                ], 10);
                
                return response()->json($errorResult, 500);
            }
            
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
                'platform_type' => 'required|string',
            ]);

            $validatedData['name'] = $validatedData['email'];
            $validatedData['status'] = '1';
            $validatedData['broker'] = 'mt4';
            $validatedData['password'] = trim($validatedData['password']);
            $validatedData['login'] = trim($validatedData['login']);
            $validatedData['environment'] = 'Real';
            $validatedData['platform_type'] = $validatedData['platform_type'];

            $tradingApiClient = new NewTradingApiClient;
            $accountExists = $tradingApiClient->accountExists($validatedData['login']);
            if ($accountExists !== '0') {
                $tradingApiClient->deleteAccount($accountExists);
            }
            $account = $tradingApiClient->createAccount($validatedData);
            $updateData = [
                'account_id' => $account->account_id,
                'status' => '1',
                'name' => $validatedData['email'],
                'groupid' => $validatedData['groupid']
            ];
            $account = $tradingApiClient->updateAccount($updateData);
            Log::info('Account created and updated', [
                'account_id' => $account->account_id,
                'login' => $account->login,
                'state' => $account->state ?? 'null',
                'status' => $account->status ?? 'null',
                'name' => $account->name ?? 'null'
            ]);
            $userAccountData = [
                'user_id' => $validatedData['user_id'],
                'login' => $validatedData['login'],
            ];
            $userAccount = UserAccount::create($userAccountData);
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
            $activityData = [
                'user_id' => $validatedData['user_id'],
                'user_account_id' => $userAccount->id,
                'activity_type' => 'connected',
                'details' => [
                    'account_number' => $account->login,
                    'account_id' => $account->account_id,
                    'current_balance' => 0,
                    'pending_balance_update' => true,
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
            $user = \App\Models\User::find($validatedData['user_id']);
            if ($user && $user->restricted_user === 1) {
                try {
                    $totalBalance = 0;
                    $account = $tradingApiClient->getAccount($account->account_id);
                    $totalBalance = $userAccount->balance ?? 0;
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
            
            // Clear cache for this user to ensure the new account appears immediately
            $cacheKey = "user_account_request_{$validatedData['user_id']}";
            cache()->forget($cacheKey);
            
            // Also clear any dashboard cache for this user
            $dashboardCacheKey = "dashboard_data_{$validatedData['user_id']}";
            cache()->forget($dashboardCacheKey);
            
            Log::info('Cache cleared after account creation', [
                'user_id' => $validatedData['user_id'],
                'account_id' => $account->account_id,
                'cleared_caches' => [$cacheKey, $dashboardCacheKey]
            ]);
            
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
            $tradingApiClient = new NewTradingApiClient;
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
                'login' => 'required|string',
                'groupid' => 'nullable|string|max:255',
                'email' => 'required|email',
            ]);
            
            $account_id = $validatedData['account_id'];
            $validatedData['status'] = '1';
            $validatedData['name'] = $validatedData['email'];
            
            // Step 1: Interact with NewTradingApiClient (this is the priority)
            $tradingApiClient = new NewTradingApiClient;
            
            try {
                $oldAccount = $tradingApiClient->getAccount($account_id);
            } catch (\Exception $e) {
                Log::error('Failed to get old account from NewTradingApiClient', [
                    'account_id' => $account_id,
                    'error' => $e->getMessage()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to get account from MetaTrader API: ' . $e->getMessage()
                ], 500);
            }
            
            $oldConfig = [
                'groupid' => $oldAccount->groupid ?? null,
            ];
            
            $apiData = [
                'account_id' => $account_id,
                'status' => '1',
                'name' => $validatedData['email'],
            ];
            
            if (isset($validatedData['groupid'])) {
                $apiData['groupid'] = $validatedData['groupid'];
            }
            
            try {
                $account = $tradingApiClient->updateAccount($apiData);
            } catch (\Exception $e) {
                Log::error('Failed to update account in NewTradingApiClient', [
                    'account_id' => $account_id,
                    'error' => $e->getMessage()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update account in MetaTrader API: ' . $e->getMessage()
                ], 500);
            }
            
            // Step 2: Database operations (these can fail without affecting the API response)
            try {
                $newConfig = [
                    'groupid' => $account->groupid ?? null,
                ];
                
                $userAccount = UserAccount::where('user_id', $validatedData['account_id'])
                    ->where('login', $validatedData['login'])
                    ->whereNull('deleted_at')
                    ->first();
                
                if ($userAccount) {
                    AccountActivity::create([
                        'user_id' => $validatedData['account_id'],
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
                } else {
                    Log::warning('User account not found for activity logging', [
                        'account_id' => $account_id,
                        'login' => $validatedData['login']
                    ]);
                }
            } catch (\Exception $e) {
                // Log database errors but don't fail the request
                Log::error('Database operation failed in update method', [
                    'account_id' => $account_id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
            
            // Clear cache for this user to ensure the updated account appears immediately
            $cacheKey = "user_account_request_{$validatedData['account_id']}";
            cache()->forget($cacheKey);
            
            // Also clear any dashboard cache for this user
            $dashboardCacheKey = "dashboard_data_{$validatedData['account_id']}";
            cache()->forget($dashboardCacheKey);
            
            // Clear account details cache
            $accountDetailsCacheKey = "account_details_{$account_id}";
            cache()->forget($accountDetailsCacheKey);
            
            Log::info('Cache cleared after account update', [
                'account_id' => $account_id,
                'cleared_caches' => [$cacheKey, $dashboardCacheKey, $accountDetailsCacheKey]
            ]);
            
            // Step 3: Return success response (NewTradingApiClient operation succeeded)
            return response()->json([
                'success' => true,
                'message' => __('MetaTrader 4 Account updated successfully!'),
                'data' => $account
            ]);
            
        } catch (\Exception $e) {
            Log::error('Unexpected error in update method', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'login' => 'required|string',
                'account_id' => 'required|string',
                'email' => 'required|email',
            ]);
            
            $account_id = $validatedData['account_id'];
            $login = $validatedData['login'];
            
            // Step 1: Interact with NewTradingApiClient first (this is the priority)
            $tradingApiClient = new NewTradingApiClient;
            $account = null;
            $accountExistsInTradingApi = false;
            
            try {
                $account = $tradingApiClient->getAccount($account_id);
                $accountExistsInTradingApi = true;
            } catch (\Exception $e) {
                Log::info('Account not found in trading API during deletion: ' . $account_id . ' - ' . $e->getMessage());
                // Create a minimal account object for logging purposes
                $account = (object) [
                    'login' => $login,
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
            
            // Step 2: Delete from NewTradingApiClient if account exists
            if ($accountExistsInTradingApi) {
                try {
                    $tradingApiClient->deleteAccount($account_id);
                } catch (\Exception $e) {
                    Log::warning('Failed to delete account from trading API: ' . $account_id . ' - ' . $e->getMessage());
                    // Don't fail the request if deletion fails
                }
            }
            
            // Step 3: Database operations (these can fail without affecting the API response)
            try {
                $userAccount = UserAccount::where('user_id', $validatedData['account_id'])
                    ->where('login', $login)
                    ->whereNull('deleted_at')
                    ->first();
                
                if ($userAccount) {
                    // Delete from database
                    UserAccount::where('user_id', $account_id)->delete();
                    
                    // Dispatch job if account was connected
                    if ($accountExistsInTradingApi && $account->state == 'CONNECTED') {
                        dispatch(new AccountWasDeletedJob([
                            'name' => $validatedData['email'],
                            'account_id' => $account->login,
                            'password' => $account->password,
                            'server' => $account->server,
                        ]));
                    }
                    
                    // Create activity log
                    AccountActivity::create([
                        'user_id' => $validatedData['account_id'],
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
                } else {
                    Log::warning('User account not found in database for deletion', [
                        'account_id' => $account_id,
                        'login' => $login
                    ]);
                }
            } catch (\Exception $e) {
                // Log database errors but don't fail the request
                Log::error('Database operation failed in destroy method', [
                    'account_id' => $account_id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
            
            // Clear cache for this user to ensure the deleted account is removed from view immediately
            $cacheKey = "user_account_request_{$validatedData['account_id']}";
            cache()->forget($cacheKey);
            
            // Also clear any dashboard cache for this user
            $dashboardCacheKey = "dashboard_data_{$validatedData['account_id']}";
            cache()->forget($dashboardCacheKey);
            
            // Clear account details cache
            $accountDetailsCacheKey = "account_details_{$account_id}";
            cache()->forget($accountDetailsCacheKey);
            
            Log::info('Cache cleared after account deletion', [
                'account_id' => $account_id,
                'cleared_caches' => [$cacheKey, $dashboardCacheKey, $accountDetailsCacheKey]
            ]);
            
            // Step 4: Return success response (NewTradingApiClient operation succeeded)
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
            $tradingApiClient = new NewTradingApiClient;
            $accounts = $tradingApiClient->getAccounts($validatedData['account_ids']);
            $accounts = collect($accounts);
            
            // Filter accounts to only include those belonging to the requested user_id
            $filteredAccounts = $accounts->filter(function ($account) use ($validatedData) {
                return $account->user_id == $validatedData['account_id'];
            });
            
            $currentTime = Carbon::now();
            
            foreach ($filteredAccounts as $account) {
                // Safely get the user account record
                $userAccount = $user->userAccounts->where('account_id', $account->account_id)->first();
                
                if ($userAccount && $userAccount->created_at) {
                    $accountCreatedAt = Carbon::parse($userAccount->created_at);
                    $timeDifferenceInMinutes = $accountCreatedAt->diffInMinutes($currentTime);
                } else {
                    // If no user account record found, assume account is ready
                    $timeDifferenceInMinutes = 10; // More than 2 minutes
                }
                
                if ($timeDifferenceInMinutes >= 2 || $account->state == 'CONNECTED') {
                    $account->state = __($account->state);
                } else {
                    $account->state = __('NONE');
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => $filteredAccounts
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
                'login' => 'required|string',
                'email' => 'required|email',
            ]);
            
            $account_id = $validatedData['account_id'];
            $login = $validatedData['login'];
            
            // Step 1: Interact with NewTradingApiClient (this is the priority)
            $tradingApiClient = new NewTradingApiClient;
            
            try {
                $account = $tradingApiClient->getAccount($account_id);
            } catch (\Exception $e) {
                Log::error('Failed to get account from NewTradingApiClient in pause', [
                    'account_id' => $account_id,
                    'error' => $e->getMessage()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to get account from MetaTrader API: ' . $e->getMessage()
                ], 500);
            }
            
            $updateData = [
                'account_id' => $account_id,
                'status' => '0',
                'name' => $validatedData['email']
            ];
            
            try {
                $tradingApiClient->updateAccount($updateData);
            } catch (\Exception $e) {
                Log::error('Failed to pause account in NewTradingApiClient', [
                    'account_id' => $account_id,
                    'error' => $e->getMessage()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to pause account in MetaTrader API: ' . $e->getMessage()
                ], 500);
            }
            
            // Step 2: Database operations (these can fail without affecting the API response)
            try {
                $userAccount = UserAccount::where('user_id', $validatedData['account_id'])
                    ->where('login', $validatedData['login'])
                    ->whereNull('deleted_at')
                    ->first();
                
                if ($userAccount) {
                    AccountActivity::create([
                        'user_id' => $account_id,
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
                } else {
                    Log::warning('User account not found for activity logging in pause', [
                        'account_id' => $account_id,
                        'login' => $validatedData['login']
                    ]);
                }
            } catch (\Exception $e) {
                // Log database errors but don't fail the request
                Log::error('Database operation failed in pause method', [
                    'account_id' => $account_id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
            
            // Clear cache for this user to ensure the paused account status is reflected immediately
            $cacheKey = "user_account_request_{$validatedData['account_id']}";
            cache()->forget($cacheKey);
            
            // Also clear any dashboard cache for this user
            $dashboardCacheKey = "dashboard_data_{$validatedData['account_id']}";
            cache()->forget($dashboardCacheKey);
            
            // Clear account details cache
            $accountDetailsCacheKey = "account_details_{$account_id}";
            cache()->forget($accountDetailsCacheKey);
            
            Log::info('Cache cleared after account pause', [
                'account_id' => $account_id,
                'cleared_caches' => [$cacheKey, $dashboardCacheKey, $accountDetailsCacheKey]
            ]);
            
            // Step 3: Return success response (NewTradingApiClient operation succeeded)
            return response()->json([
                'success' => true,
                'message' => __('Account paused successfully')
            ]);
            
        } catch (\Exception $e) {
            Log::error('Unexpected error in pause method', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
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
                'login' => 'required|string',
                'email' => 'required|email',
            ]);
            
            $account_id = $validatedData['account_id'];
            
            // Step 1: Interact with NewTradingApiClient (this is the priority)
            $tradingApiClient = new NewTradingApiClient;
            
            try {
                $account = $tradingApiClient->getAccount($account_id);
            } catch (\Exception $e) {
                Log::error('Failed to get account from NewTradingApiClient in resume', [
                    'account_id' => $account_id,
                    'error' => $e->getMessage()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to get account from MetaTrader API: ' . $e->getMessage()
                ], 500);
            }
            
            $updateData = [
                'account_id' => $account_id,
                'status' => '1',
                'name' => $validatedData['email']
            ];
            
            try {
                $tradingApiClient->updateAccount($updateData);
            } catch (\Exception $e) {
                Log::error('Failed to resume account in NewTradingApiClient', [
                    'account_id' => $account_id,
                    'error' => $e->getMessage()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to resume account in MetaTrader API: ' . $e->getMessage()
                ], 500);
            }
            
            // Step 2: Database operations (these can fail without affecting the API response)
            try {
                $userAccount = UserAccount::where('user_id', $validatedData['account_id'])
                    ->where('login', $validatedData['login'])
                    ->whereNull('deleted_at')
                    ->first();
                
                if ($userAccount) {
                    AccountActivity::create([
                        'user_id' => $validatedData['account_id'],
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
                } else {
                    Log::warning('User account not found for activity logging in resume', [
                        'account_id' => $account_id,
                        'login' => $validatedData['login']
                    ]);
                }
            } catch (\Exception $e) {
                // Log database errors but don't fail the request
                Log::error('Database operation failed in resume method', [
                    'account_id' => $account_id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
            
            // Clear cache for this user to ensure the resumed account status is reflected immediately
            $cacheKey = "user_account_request_{$validatedData['account_id']}";
            cache()->forget($cacheKey);
            
            // Also clear any dashboard cache for this user
            $dashboardCacheKey = "dashboard_data_{$validatedData['account_id']}";
            cache()->forget($dashboardCacheKey);
            
            // Clear account details cache
            $accountDetailsCacheKey = "account_details_{$account_id}";
            cache()->forget($accountDetailsCacheKey);
            
            Log::info('Cache cleared after account resume', [
                'account_id' => $account_id,
                'cleared_caches' => [$cacheKey, $dashboardCacheKey, $accountDetailsCacheKey]
            ]);
            
            // Step 3: Return success response (NewTradingApiClient operation succeeded)
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
            $userAccount = UserAccount::where('user_id', $user_id)
                ->where('account_id', $account_id)
                ->whereNull('deleted_at')
                ->first();
            if (!$userAccount) {
                Log::warning('User account not found', [
                    'account_id' => $account_id,
                    'user_id' => $user_id
                ]);
                $activity = AccountActivity::where('user_id', $user_id)
                    ->where('activity_type', 'connected')
                    ->latest()
                    ->first();
            } else {
                $activity = AccountActivity::where('user_id', $user_id)
                    ->where('user_account_id', $userAccount->id)
                    ->where('activity_type', 'connected')
                    ->latest()
                    ->first();
            }
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
            if ($currentBalance != 0 && !$isPendingUpdate) {
                Log::info('Activity already has non-zero balance, no update needed', [
                    'account_id' => $account_id,
                    'current_balance' => $currentBalance
                ]);
                $tradingApiClient = new NewTradingApiClient;
                $user = \App\Models\User::find($user_id);
                $restrictedStatusRemoved = false;
                if ($user && $user->restricted_user === 1) {
                    try {
                        $totalBalance = 0;
                        $account = $tradingApiClient->getAccount($user_id);
                        $totalBalance += $account->balance ?? 0;
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
                    'status' => 1,
                    'restricted_status_removed' => $restrictedStatusRemoved
                ]);
            }
            // Try to get fresh account data from Controller API (real terminal data)
            $freshAccount = null;
            $terminalStatus = null;
            
            // Fallback to NewTradingApiClient if Controller API fails
            try {
                $tradingApiClient = new NewTradingApiClient;
                $freshAccount = $tradingApiClient->getAccount($account_id);
                Log::info('Fallback: Account data retrieved from NewTradingApiClient', [
                    'account_id' => $account_id,
                    'balance' => $freshAccount->balance ?? 'null',
                    'state' => $freshAccount->state ?? 'null'
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to get account from NewTradingApiClient (fallback)', [
                    'account_id' => $account_id,
                    'error' => $e->getMessage()
                ]);
                
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
                    $retryAfter = min(30 * ($details['retry_count'] ?? 1), 300);
                    return response()->json([
                        'success' => false,
                        'message' => 'Account is being processed. Please try again in a few moments.',
                        'retry_after' => $retryAfter,
                        'retry_count' => $details['retry_count'] ?? 1
                    ], 202);
                }
                throw $e;
            }
            if ($freshAccount) {
                $details = $activity->details;
                $details['current_balance'] = $freshAccount->balance ?? 0;
                $details['pending_balance_update'] = false;
                $details['account_state'] = $freshAccount->state ?? 'UNKNOWN';
                $details['terminal_status'] = $terminalStatus ?? 'unknown';
                $details['balance_updated_at'] = now()->toISOString();
                $details['data_source'] = $terminalStatus ? 'controller_api' : 'trading_api_client';
                unset($details['update_error']);
                $activity->update([
                    'details' => $details
                ]);
                $user = \App\Models\User::find($user_id);
                $restrictedStatusRemoved = false;
                if ($user && $user->restricted_user === 1) {
                    try {
                        $totalBalance = 0;
                        $account = $tradingApiClient->getAccount($account_id);
                        $totalBalance = $userAccount->balance ?? 0;
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
                    'account_state' => $freshAccount->state,
                    'terminal_status' => $terminalStatus ?? 'unknown',
                    'data_source' => $terminalStatus ? 'controller_api' : 'trading_api_client'
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Activity balance updated successfully',
                    'balance' => $freshAccount->balance,
                    'state' => $freshAccount->state,
                    'status' => $freshAccount->status,
                    'restricted_status_removed' => $restrictedStatusRemoved,
                    'groupid' => $freshAccount->groupid,
                    'freshAccount' => $freshAccount
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
                'account_id' => 'required|string',
                'user_id' => 'required|integer|exists:users,id',
            ]);
            $userAccount = UserAccount::where('user_id', $validatedData['user_id'])
                ->where('account_id', $validatedData['account_id'])
                ->whereNull('deleted_at')
                ->first();
            if (!$userAccount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account not found in our system'
                ], 404);
            }
            try {
                $initialLot = $userAccount->initial_lot;
                $maxPairs = $userAccount->max_pairs;
                $activePairs = $userAccount->active_pairs;
            } catch (\Exception $e) {
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
                'login' => 'required|string',
                'user_id' => 'required|integer|exists:users,id',
                'initial_lot' => 'nullable|numeric|min:0.01|max:100',
                'max_pairs' => 'nullable|string|max:255',
                'active_pairs' => 'nullable|array',
                'active_pairs.*' => 'string|max:10'
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
            try {
                $userAccount->update([
                    'initial_lot' => $validatedData['initial_lot'] ?? null,
                    'max_pairs' => $validatedData['max_pairs'] ?? null,
                    'active_pairs' => $validatedData['active_pairs'] ?? []
                ]);
            } catch (\Exception $e) {
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
            try {
                $userAccount->update([
                    'initial_lot' => 0.01,
                    'max_pairs' => '5',
                    'active_pairs' => []
                ]);
            } catch (\Exception $e) {
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
     * Forward EA data to external API
     */
    public function getEaData(Request $request)
    {
        try {
            // Get the request data
            $requestData = $request->all();
            
            Log::info('EA Data received', [
                'data' => $requestData['data'],
                'headers' => $request->headers->all()
            ]);
            
            // Forward the request to the external API
            $response = Http::timeout(30)
                ->retry(2, 1000)
                ->post('https://api.jamestradinggroup.com/api/ea-data', $requestData['data']);
            
            if ($response->successful()) {
                Log::info('EA Data forwarded successfully', [
                    'external_response' => $response->json()
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'EA Data forwarded successfully',
                    'external_response' => $response->json(),
                    'timestamp' => now()->toISOString()
                ]);
            } else {
                Log::error('External API failed', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'External API failed',
                    'error' => $response->body(),
                    'status' => $response->status()
                ], $response->status());
            }
            
        } catch (\Exception $e) {
            Log::error('EA Data API Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to forward EA data: ' . $e->getMessage(),
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }

    /**
     * Get detailed account information from external API
     */
    public function getAccountDetail($account_id)
    {
        try {
            // Call the external API to get account details
            $response = Http::timeout(60)
                ->retry(2, 1000)
                ->get("https://api.jamestradinggroup.com/api/detail/{$account_id}");

            if ($response->successful()) {
                return response()->json($response->json());
            }

            Log::error('NewAccountController - getAccountDetail failed', [
                'account_id' => $account_id,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get account details',
                'error' => $response->body()
            ], $response->status());

        } catch (\Exception $e) {
            Log::error('NewAccountController - getAccountDetail exception', [
                'account_id' => $account_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'MetaTrader API server is currently unavailable',
                'error' => 'Connection timeout or server unreachable',
                'timestamp' => now()->toISOString()
            ], 503);
        }
    }

    /**
     * Simple test endpoint to debug 405 error
     */
    public function testSimpleEndpoint()
    {
        return response()->json([
            'success' => true,
            'message' => 'Simple endpoint is working',
            'timestamp' => now()->toISOString()
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

            // Send the email
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
                    'message' => 'Failed to send AccountWasDeleted notification'
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

    /**
     * Get detailed account information from external API
     */
    public function getAccountProgress($account_id)
    {
        try {
            // Call the external API to get account details
            $response = Http::timeout(60)
                ->retry(2, 1000)
                ->get("https://api.jamestradinggroup.com/api/detail/status/{$account_id}");

            if ($response->successful()) {
                return response()->json($response->json());
            }

            Log::error('NewAccountController - getAccountProgress failed', [
                'account_id' => $account_id,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get account details',
                'error' => $response->body()
            ], $response->status());

        } catch (\Exception $e) {
            Log::error('NewAccountController - getAccountProgress exception', [
                'account_id' => $account_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'MetaTrader API server is currently unavailable',
                'error' => 'Connection timeout or server unreachable',
                'timestamp' => now()->toISOString()
            ], 503);
        }
    }
} 