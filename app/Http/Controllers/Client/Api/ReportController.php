<?php

namespace App\Http\Controllers\Client\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function index($user_id)
    {
        try {
            $start = microtime(true);
            $user = \App\Models\User::findOrFail($user_id);
            $user_account = $user->userAccounts->first();

            if ($user_account) {
                // Load reports from cache
                $data = Storage::disk('local')->get('leaderboard-data.json');
                $users_data = json_decode($data, true);

                $users_data = array_filter($users_data, function($report) {
                    return strpos($report['name'], '@') !== false;
                });
                
                $total_data = null;

                foreach ($users_data as $report) {
                    if ($report['login'] == $user_account->login) {
                        $total_data = $report;
                        Log::info('Found user data in leaderboard', [
                            'user_id' => $user_id,
                            'login' => $user_account->login,
                            'total_data' => $total_data
                        ]);
                        break;
                    }
                }
                
                if (!$total_data) {
                    Log::warning('User data not found in leaderboard', [
                        'user_id' => $user_id,
                        'login' => $user_account->login,
                        'available_logins' => array_column($users_data, 'login')
                    ]);
                }

                $data = Storage::disk('local')->get('currentyearReport.json');
                $users_data = json_decode($data, true);

                $users_data = array_filter($users_data, function($report) {
                    return strpos($report['name'], '@') !== false;
                });

                $month_data_arr = [];

                foreach ($users_data as $report) {
                    if ($report['login'] == $user_account->login) {
                        $month_data_arr[] = $report;
                    }
                }
                
                Log::info('Monthly data found for user', [
                    'user_id' => $user_id,
                    'login' => $user_account->login,
                    'month_data_count' => count($month_data_arr),
                    'month_data' => $month_data_arr
                ]);

                // Initialize arrays with default values
                $pnl_arr = array_fill(0, 12, 0);
                $balance_arr = array_fill(0, 12, 0);

                // Replace default values with actual data
                foreach ($month_data_arr as $data) {
                    $pnl_arr[$data['month'] - 1] = $data['pnl'];
                    $balance_arr[$data['month'] - 1] = $data['balance_end'];
                }

                // Calculate total PnL from the array
                $total_pnl = array_sum($pnl_arr);
                
                // Calculate advanced stats
                $current_month = date('n') - 1; // 0-based index
                $current_year = date('Y');
                
                $advanced_stats = [
                    'today' => 0, // Would need daily data to calculate
                    'week' => 0,  // Would need weekly data to calculate
                    'month' => $pnl_arr[$current_month] ?? 0,
                    'six_months' => array_sum(array_slice($pnl_arr, max(0, $current_month - 5), 6)),
                    'year' => $total_pnl
                ];
                
                Log::info('Calculated report data', [
                    'user_id' => $user_id,
                    'login' => $user_account->login,
                    'total_pnl' => $total_pnl,
                    'pnl_arr' => $pnl_arr,
                    'advanced_stats' => $advanced_stats
                ]);
                
                $endReports = microtime(true);
                Log::channel('web')->info('ReportController : Time to load reports from cache: ' . ($endReports - $start) . ' seconds');

                return response()->json([
                    'success' => true,
                    'data' => [
                        'total_data' => $total_data,
                        'pnl' => $total_pnl,
                        'pnl_arr' => $pnl_arr,
                        'balance_arr' => $balance_arr,
                        'advanced_stats' => $advanced_stats
                    ]
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => null
            ]);

        } catch (\Exception $e) {
            Log::error('Report API Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
} 