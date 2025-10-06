<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    //
    public function index(Request $request)
    {
        $start = microtime(true);
        $user = auth()->user();
        try {
            $user_account = $user->userAccounts->first();

            if ($user_account != null) {
                // Load reports from cache
                $data = Storage::disk('local')->get('leaderboard-data.json');
                $users_data = json_decode($data, true);

                $users_data = array_filter($users_data, function($report) {
                    return strpos($report['name'], '@') !== false;
                });
                
                $total_data = null;

                foreach ($users_data as $report) {
                    if ($report['login'] == $user_account->login)
                        $total_data = $report;
                }

                $data = Storage::disk('local')->get('currentyearReport.json');
                $users_data = json_decode($data, true);

                $users_data = array_filter($users_data, function($report) {
                    return strpos($report['name'], '@') !== false;
                });

                $month_data_arr = [];

                foreach ($users_data as $report) {
                    if ($report['login'] == $user_account->login)
                        $month_data_arr[] = $report;
                }

                // Initialize an array with all pnl_arr and default pnl value of 0
                $pnl_arr = [];
                for ($i = 1; $i <= 12; $i++) {
                    $pnl_arr[$i] = 0;
                }

                // Replace default values with actual data
                foreach ($month_data_arr as $data) {
                    $pnl_arr[$data['month']] = $data['pnl'];
                }

                // Convert to JSON for use in the JavaScript
                $pnl_arr = array_values($pnl_arr); // To ensure the array is in the correct order

                $balance_arr = [];
                for ($i = 1; $i <= 12; $i++) {
                    $balance_arr[$i] = 0;
                }

                // Replace default values with actual data
                foreach ($month_data_arr as $data) {
                    $balance_arr[$data['month']] = $data['balance_end'];
                }

                // Convert to JSON for use in the JavaScript
                $balance_arr = array_values($balance_arr); // To ensure the array is in the correct order

                $endReports = microtime(true);
                Log::channel('web')->info('ReportController : Time to load reports from cache: ' . ($endReports - $start) . ' seconds');

                return View('client.reports.index', compact('total_data', 'pnl_arr', 'balance_arr'));
            }
            return View('client.reports.index');
        } catch (\Exception $e) {
            return View('client.reports.index')->with('error', $e->getMessage());
        }
    }
}
