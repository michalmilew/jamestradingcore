<?php

namespace App\Http\Controllers\Client\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\NewTradingApiClient;
use App\Models\UserAccount;
use App\Models\User;
use Carbon\Carbon;

class ControllerApiController extends Controller
{
    /**
     * Get all accounts for the MT4/MT5 controller
     * This endpoint is specifically for the controller to sync all accounts
     */
    public function getAllAccounts()
    {
        try {
            // Get all user accounts from the database
            $userAccounts = UserAccount::with('user')->get();
            
            if ($userAccounts->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'No accounts found'
                ]);
            }
            
            // Get all account IDs
            $accountIds = $userAccounts->pluck('account_id')->toArray();
            
            // Fetch account details from trading API
            $tradingApiClient = new NewTradingApiClient;
            $accounts = $tradingApiClient->getAccounts($accountIds);
            $accounts = collect($accounts);
            
            // Process each account and add user information
            $processedAccounts = [];
            foreach ($accounts as $account) {
                // Find the user account record
                $userAccount = $userAccounts->where('account_id', $account->account_id)->first();
                
                if ($userAccount) {
                    // Add user information to the account
                    $account->user_id = $userAccount->user->id;
                    $account->user_email = $userAccount->user->email;
                    $account->user_name = $userAccount->user->name;
                    
                    // Process account state based on creation time
                    $currentTime = Carbon::now();
                    $accountCreatedAt = Carbon::parse($userAccount->created_at);
                    $timeDifferenceInMinutes = $accountCreatedAt->diffInMinutes($currentTime);
                    
                    if ($timeDifferenceInMinutes >= 2 || $account->state == 'CONNECTED') {
                        $account->state = __($account->state);
                    } else {
                        $account->state = __('NONE');
                    }
                    
                    $processedAccounts[] = $account;
                }
            }
            
            Log::info('Controller API: Retrieved all accounts', [
                'total_accounts' => count($processedAccounts),
                'account_ids' => $accountIds
            ]);
            
            return response()->json([
                'success' => true,
                'data' => $processedAccounts,
                'total_accounts' => count($processedAccounts)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Controller API Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get server usage statistics for the controller
     */
    public function getServerUsage()
    {
        try {
            // Get all user accounts
            $userAccounts = UserAccount::with('user')->get();
            $accountIds = $userAccounts->pluck('account_id')->toArray();
            
            // Get account states from trading API
            $tradingApiClient = new NewTradingApiClient;
            $accounts = $tradingApiClient->getAccounts($accountIds);
            $accounts = collect($accounts);
            
            // Count active accounts (CONNECTED state)
            $activeAccounts = $accounts->where('state', 'CONNECTED')->count();
            $totalAccounts = $accounts->count();
            
            // Calculate total balance
            $totalBalance = $accounts->sum('balance');
            
            $serverUsage = [
                'localhost' => [
                    'cpu_usage' => 0.0, // This would be calculated from system metrics
                    'memory_usage' => 0.0, // This would be calculated from system metrics
                    'active_terminals' => $activeAccounts,
                    'total_terminals' => $totalAccounts,
                    'total_balance' => $totalBalance,
                    'last_updated' => now()->toISOString()
                ]
            ];
            
            return response()->json([
                'success' => true,
                'data' => $serverUsage
            ]);
            
        } catch (\Exception $e) {
            Log::error('Controller API Server Usage Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
} 