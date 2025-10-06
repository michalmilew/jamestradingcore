<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TradingApiReport;
use App\Models\TradingApiClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SaveTopUsers extends Command
{
    protected $signature = 'save:topuser';
    protected $description = 'Save top users';

    public function handle()
    {
        Log::channel('command')->info("============== SaveTopUsers START  ==============");
        $tradingApiReport = new TradingApiReport;

        // Initialize Trading API Client
        $tradingApiClient = new TradingApiClient;
        $accounts = $tradingApiClient->getAccounts();

        // Convert accounts to a collection for easier manipulation
        $accounts = collect($accounts);

        // Retrieve reports for both this year and last year
        $currentYearUsers = $tradingApiReport->getReports(date("Y"));
        $lastYearUsers = $tradingApiReport->getReports(date("Y", strtotime('-1 year')));

        // Merge the reports for both years
        $users = array_merge($currentYearUsers, $lastYearUsers);

        // Initialize an associative array to hold aggregated user data
        $aggregatedUsers = [];

        foreach ($users as $user) {
            // Ensure $user is treated as an array
            $user = (array) $user;

            foreach ($accounts as $account) {
                if ($account['name'] == $user['name']) {
                    $user['user_name'] = $account['account'];
                }
            }

            $email = $user['name'];

            // If the user email already exists in the aggregated array, sum the pnl and pnlEUR
            if (isset($aggregatedUsers[$email])) {
                $aggregatedUsers[$email]['pnl'] += $user['pnl'];
                $aggregatedUsers[$email]['pnlEUR'] += $user['pnlEUR'];
            } else {
                // If it's the first time encountering this email, initialize the data
                $aggregatedUsers[$email] = $user;
            }
        }

        // Convert the associative array to a normal array
        $modifiedUsers = array_values($aggregatedUsers);

        // Check if leaderboard-data.json exists and delete it if it does
        if (Storage::disk('local')->exists('leaderboard-data.json')) {
            Log::channel('command')->info('Leaderboard data deleted.');
            Storage::disk('local')->delete('leaderboard-data.json');
        }

        // Save the modified JSON data to leaderboard-data.json in the local storage
        Storage::disk('local')->put('leaderboard-data.json', json_encode($modifiedUsers));

        // Check if leaderboard-data.json exists and delete it if it does
        if (Storage::disk('local')->exists('currentyearReport.json')) {
            Log::channel('command')->info('Current Year Report data deleted.');
            Storage::disk('local')->delete('currentyearReport.json');
        }

        // Save the modified JSON data to leaderboard-data.json in the local storage
        Storage::disk('local')->put('currentyearReport.json', json_encode($currentYearUsers));

        Log::channel('command')->info("============== SaveTopUsers END  ==============");
    }
}
