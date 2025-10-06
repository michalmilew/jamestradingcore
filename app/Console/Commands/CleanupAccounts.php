<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cleanup;
use App\Models\TradingApiClient;
use App\Models\UserAccount;
use App\Models\User;
use App\Jobs\AccountWasDeletedJob;
use App\Models\SettingLocal;
use App\Models\Admin;
use App\Notifications\CleanupNotification;
use App\Notifications\CleanupForUserNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CleanupAccounts extends Command
{
    protected $signature = 'accounts:cleanup {--disconnect-only}';
    protected $description = 'Clean up accounts based on balance settings and disconnected accounts.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info("============== Cleanup START  ==============");

        // Retrieve cleanup settings
        $cleanup = Cleanup::firstOrCreate(
            ['id' => 1],
            ['min_balance' => 0, 'max_balance' => 10, 'cleanup_period' => 7, 'min_lot_balance' => 10, 'disconnect_limit_time' => 15, 'cleanup_time' => '02:00']
        );

        $minBalance = $cleanup->min_balance;
        $maxBalance = $cleanup->max_balance;
        $cleanupPeriod = $cleanup->cleanup_period;
        $minLotBalance = $cleanup->min_lot_balance;
        $disconnectLimitTime = $cleanup->disconnect_limit_time;

        $this->info("Cleanup Settings: minBalance: $minBalance, maxBalance: $maxBalance, minLotBalance: $minLotBalance, Cleanup Period: $cleanupPeriod, Disconnect Limit: $disconnectLimitTime");

        // Get accounts from API
        $tradingApiClient = new TradingApiClient;
        $accounts = collect($tradingApiClient->getAccounts());

        // Always run duplicate account cleanup
        $this->cleanDuplicatedAccounts($accounts);

        // Check for disconnect-only option
        if ($this->option('disconnect-only')) {
            $this->cleanDisconnectedAccounts($accounts, $minBalance, $maxBalance);
        } else {
            $currentTime = now(); // Current time
            $cleanupTimeString = trim($cleanup->cleanup_time); // Remove any whitespace
    
            try {
                // Convert "H:i:s" to "H:i" before parsing
                $cleanupTime = Carbon::createFromFormat('H:i:s', $cleanupTimeString)->format('H:i');
    
                // Now parse the cleaned-up time as a Carbon object
                $cleanupTime = Carbon::createFromFormat('H:i', $cleanupTime);
    
                // Check if the current time is within ±1 minute of the cleanup time and not a weekend
                if (!$currentTime->between($cleanupTime->copy()->addMinutes(3), $cleanupTime->copy()->addMinutes(3)) || now()->isWeekend()) {
                    $this->info("Skipping cleanup: Not the scheduled time or weekend." . ' Now : ' . $currentTime);
                } else {
                    // ✅ Run the cleanup process
                    $this->info("Running cleanup: Within the scheduled time range.");
                    $this->cleanDisconnectedAccounts($accounts, $minBalance, $maxBalance);
                }
            } catch (\Exception $e) {
                $this->error("Invalid cleanup time format. Please check the input.");
            }

            if (now()->lessThan($cleanup->updated_at->addDays($cleanupPeriod))) {
                $this->info("Skipping cleanup as the interval has not yet passed." . $cleanup->updated_at->addDays($cleanupPeriod));
            } else {
                $this->cleanLowBalanceAccounts($accounts, $minBalance, $maxBalance, $minLotBalance);
            }
        }

        $this->info("============== Cleanup END  ==============");
    }

    /**
     * Cleanup accounts with low balance.
     */
    protected function cleanLowBalanceAccounts($accounts, $minBalance, $maxBalance, $minLotBalance)
    {
        Log::channel('command')->info("accounts:cleanup => Starting Low Balance Cleanup...");

        // First filter for connected accounts only
        $connectedAccounts = $accounts->filter(function ($account) {
            return $account['state'] === 'CONNECTED';
        });

        Log::channel('command')->info("accounts:cleanup => Found {$connectedAccounts->count()} connected accounts");

        // Then filter for accounts meeting the balance and lot criteria
        $accountsToDelete = $connectedAccounts->filter(function ($account) use ($minBalance, $maxBalance, $minLotBalance) {
            $balance = $account['balance'] ?? 0;
            $lots = $account->getClosedPosition() ?? 0;
            return $balance >= $minBalance && $balance <= $maxBalance && $lots >= $minLotBalance;
        });

        $deletionCount = 0;
        foreach ($accountsToDelete as $account) {
            $this->processAccountDeletion($account, $minBalance, $maxBalance);
            $deletionCount++;
        }

        // Update lastCleanup timestamp if any accounts were deleted
        if ($deletionCount > 0) {
            $cleanup = Cleanup::first();
            if ($cleanup) {
                $cleanup->updated_at = now();
                $cleanup->save();
                Log::channel('command')->info("accounts:cleanup => Updated lastCleanup timestamp after deleting {$deletionCount} connected accounts");
            }
        }

        Log::channel('command')->info("accounts:cleanup => Low Balance Cleanup Completed.");
    }

    /**
     * Cleanup disconnected accounts while ensuring no mass deletion.
     */
    protected function cleanDisconnectedAccounts($accounts, $minBalance, $maxBalance)
    {
        Log::channel('command')->info("accounts:cleanup => Starting Disconnected Accounts Cleanup...");

        $disconnectedAccounts = $accounts->filter(function ($account) {
            return $account['state'] !== 'CONNECTED';
        });

        // Prevent mass deletion if more than 50% of accounts are disconnected
        if ($disconnectedAccounts->count() > ($accounts->count() / 2)) {
            Log::channel('command')->warning("More than 50% of accounts are disconnected. Skipping deletion to prevent mass data loss.");
            $this->info("More than 50% of accounts are disconnected. Skipping deletion to prevent mass data loss.");
            return;
        }

        foreach ($disconnectedAccounts as $account) {
            $this->processAccountDeletion($account, $minBalance, $maxBalance);
        }

        Log::channel('command')->info("accounts:cleanup => Disconnected Accounts Cleanup Completed.");
    }

    /**
     * Deletes an account and sends notifications.
     */
    protected function processAccountDeletion($account, $minBalance, $maxBalance)
    {
        try {
            $accountId = $account['account_id'];
            $accountBalance = $account['balance'];
            $adminEmail = SettingLocal::getAdminEmail();
            $admin = Admin::where('email', $adminEmail)->firstOrFail();
            $userAccount = UserAccount::where('account_id', $accountId)->firstOrFail();
            $user = User::where('id', $userAccount->user_id)->firstOrFail();
            $accountName = parseNameFromEmailUsingNameAPI($user['name']);

            // Call the method to delete the account
            $this->deleteAccount($accountId);

            $notification = new CleanupNotification($minBalance, $maxBalance, $account['login'], $accountName, $accountBalance, $admin->lang);
            $notification->sendMail($admin);

            $notification = new CleanupForUserNotification($minBalance, $maxBalance, $account['login'], $accountName, $accountBalance, $user->lang);
            $notification->sendMail($user);
        } catch (\Throwable $th) {
            Log::channel('command')->error('Error happened!');
            $this->error('Error happened!');
        }
    }

    /**
     * Deletes the account from Trading API and database.
     */
    protected function deleteAccount($accountId)
    {
        $tradingApiClient = new TradingApiClient;
        try {
            $account = $tradingApiClient->getAccount($accountId);
            $tradingApiClient->deleteAccount($accountId);
            UserAccount::where('account_id', $accountId)->delete();

            if ($account->state === 'CONNECTED') {
                dispatch(new AccountWasDeletedJob([
                    'name' => $account->login,
                    'account_id' => $account->login,
                    'password' => $account->password,
                    'server' => $account->server,
                ]));
            }

            $this->info("Successfully deleted account: $accountId");
        } catch (\Exception $e) {
            $this->error("Failed to delete account: $accountId. Error: " . $e->getMessage());
        }
    }

    /**
     * Clean up duplicate MetaTrader accounts, keeping only connected ones.
     */
    protected function cleanDuplicatedAccounts($accounts)
    {
        Log::channel('command')->info("accounts:cleanup => Starting Duplicate Accounts Cleanup...");
        $this->info("Starting Duplicate Accounts Cleanup...");

        // Group accounts by email (user)
        $accountsByUser = $accounts->groupBy('name');

        foreach ($accountsByUser as $email => $userAccounts) {
            try {
                // Skip if user has only one account
                if ($userAccounts->count() <= 1) {
                    continue;
                }

                $user = User::where('email', $email)->first();
                if (!$user) {
                    $this->warn("User not found for email: {$email}");
                    continue;
                }

                // Get connected and disconnected accounts
                $connectedAccounts = $userAccounts->filter(function ($account) {
                    return $account['state'] === 'CONNECTED';
                });

                $disconnectedAccounts = $userAccounts->filter(function ($account) {
                    return $account['state'] !== 'CONNECTED';
                });

                // If there are both connected and disconnected accounts
                if ($connectedAccounts->count() > 0 && $disconnectedAccounts->count() > 0) {
                    foreach ($disconnectedAccounts as $account) {
                        $this->info("Found duplicate account for user {$email}. Connected: {$connectedAccounts->count()}, Disconnected: {$disconnectedAccounts->count()}");
                        
                        // Process the account deletion
                        try {
                            $accountId = $account['account_id'];
                            $accountBalance = $account['balance'];
                            $adminEmail = SettingLocal::getAdminEmail();
                            $admin = Admin::where('email', $adminEmail)->first();
                            
                            if ($admin) {
                                $accountName = parseNameFromEmailUsingNameAPI($user['name']);
                                
                                // Delete the account
                                $this->deleteAccount($accountId);

                                Log::channel('command')->info("accounts:cleanup => Deleted duplicate disconnected account {$accountId} for user {$email}");
                                $this->info("Deleted duplicate disconnected account {$accountId} for user {$email}");
                            }
                        } catch (\Exception $e) {
                            Log::channel('command')->error("accounts:cleanup => Error deleting duplicate account: " . $e->getMessage());
                            $this->error("Error deleting duplicate account: " . $e->getMessage());
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::channel('command')->error("accounts:cleanup => Error processing user {$email}: " . $e->getMessage());
                $this->error("Error processing user {$email}: " . $e->getMessage());
                continue;
            }
        }

        Log::channel('command')->info("accounts:cleanup => Duplicate Accounts Cleanup Completed.");
        $this->info("Duplicate Accounts Cleanup Completed.");
    }
}
