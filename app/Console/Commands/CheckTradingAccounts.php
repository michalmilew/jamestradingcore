<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Cleanup;
use App\Models\TradingApiClient;

class CheckTradingAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:trading-accounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check all users for trading accounts and log inactive accounts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking trading accounts for all users.');

        // Retrieve cleanup settings from the database
        $cleanup = Cleanup::firstOrCreate(
            ['id' => 1], // Assuming only one record should exist
            ['inactive_period' => 15]
        );

        $inactive_period = $cleanup->inactive_period;

        $tradingApiClient = new TradingApiClient;
        $accounts = $tradingApiClient->getAccounts();
        $accounts = collect($accounts);

        // Fetch all users
        $users = User::all();

        foreach ($users as $user) {
            // Check if user's email exists in accounts collection and account is connected
            $hasActiveAccount = $accounts->contains(function ($account) use ($user) {
                return $account->name === $user->email && $account->state === 'CONNECTED';
            });

            if ($hasActiveAccount) {
                $user->update(['inactive' => null]);
            } else if ($user->restricted_user == 0) {
                $this->info("User {$user->id} has no active trading account.");
                $this->logInactiveAccount($user, $inactive_period);
            }
        }

        $this->info('Finished checking trading accounts.');
    }

    /**
     * Log inactive accounts and check the timestamp.
     *
     * @param $user
     */
    private function logInactiveAccount($user, $inactive_period)
    {
        $inactiveTimestamp = $user->inactive;

        if (!$inactiveTimestamp) {
            // Register the current timestamp if inactive is null
            $user->update(['inactive' => Carbon::now()]);
            $inactiveTimestamp = Carbon::now();
        }

        $inactiveTime = Carbon::parse($inactiveTimestamp);
        $elapsedTime = Carbon::now()->diffInDays($inactiveTime);

        $this->info("Account {$user->id} is elapsedTime {$elapsedTime} day.");

        if ($elapsedTime > $inactive_period) {
            Log::channel('command')->alert("check:trading-accounts => Account {$user->id} is inactive for more than {$inactive_period} day. RED FLAG!");
            $this->error("Account {$user->id} is inactive for more than {$inactive_period} day. RED FLAG!");
            $user->update(['restricted_user' => 1, 'inactive' => null]);
        } else {
            $this->warn("Account {$user->id} is inactive but within {$inactive_period} day.");
        }
    }
}
