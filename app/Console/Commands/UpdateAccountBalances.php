<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AccountActivity;
use App\Models\TradingApiClient;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class UpdateAccountBalances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accounts:update-balances';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update account balances for recently connected accounts with 0 balance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting account balance update...');

        try {
            // Get activities from the last 10 minutes that are 'connected' with balance 0
            $tenMinutesAgo = Carbon::now()->subMinutes(10);
            
            // First, let's see all activities from the last 10 minutes
            $allActivities = AccountActivity::where('created_at', '>=', $tenMinutesAgo)->get();
            $this->info("Total activities in last 10 minutes: {$allActivities->count()}");
            
            // Show details of each activity
            foreach ($allActivities as $activity) {
                $this->info("Activity ID: {$activity->id}, Type: {$activity->activity_type}, Balance: " . ($activity->details['current_balance'] ?? 'null'));
            }
            
            $activities = AccountActivity::where('activity_type', 'connected')
                ->where('created_at', '>=', $tenMinutesAgo)
                ->get()
                ->filter(function ($activity) {
                    // Filter activities where current_balance is 0
                    $details = $activity->details;
                    $balance = $details['current_balance'] ?? null;
                    $this->info("Checking activity {$activity->id}: balance = " . ($balance ?? 'null'));
                    return isset($details['current_balance']) && $details['current_balance'] == 0;
                });

            $this->info("Found {$activities->count()} activities to update");

            if ($activities->isEmpty()) {
                $this->info('No activities found to update');
                return 0;
            }

            $tradingApiClient = new TradingApiClient;
            $updatedCount = 0;
            $errorCount = 0;

            foreach ($activities as $activity) {
                try {
                    $accountNumber = $activity->details['account_number'] ?? null;
                    $accountId = $activity->details['account_id'] ?? null;
                    
                    if (!$accountNumber || !$accountId) {
                        $this->warn("Activity {$activity->id} has no account number or account_id");
                        continue;
                    }

                    $this->info("Fetching balance for account: {$accountNumber} (ID: {$accountId})");

                    // Get fresh account data using account_id
                    try {
                        $freshAccount = $tradingApiClient->getAccount($accountId);
                        if ($freshAccount && isset($freshAccount->balance)) {
                            $oldBalance = $activity->details['current_balance'] ?? 0;
                            $newBalance = $freshAccount->balance ?? 0;
                            // Update the activity with new balance
                            $details = $activity->details;
                            $details['current_balance'] = $newBalance;
                            $details['account_state'] = $freshAccount->state ?? 'UNKNOWN';
                            $details['balance_updated_at'] = now()->toISOString();
                            $activity->update([
                                'details' => $details
                            ]);
                            $this->info("Updated account {$accountNumber}: {$oldBalance} → {$newBalance}");
                            $updatedCount++;
                            Log::info('Account balance updated via command', [
                                'account_id' => $accountId,
                                'old_balance' => $oldBalance,
                                'new_balance' => $newBalance,
                                'activity_id' => $activity->id
                            ]);
                        } else {
                            throw new \Exception('No balance returned');
                        }
                    } catch (\Exception $e) {
                        // If account does not exist, mark as unavailable
                        $details = $activity->details;
                        $details['current_balance'] = null;
                        $details['account_state'] = 'NOT_FOUND';
                        $details['balance_update_note'] = 'Account not found in trading API';
                        $details['balance_updated_at'] = now()->toISOString();
                        $activity->update([
                            'details' => $details
                        ]);
                        $this->warn("Account {$accountNumber} not found. Marked as unavailable.");
                        Log::warning('Account not found during balance update', [
                            'account_id' => $accountId,
                            'activity_id' => $activity->id,
                            'error' => $e->getMessage()
                        ]);
                        $errorCount++;
                    }

                } catch (\Exception $e) {
                    $this->error("Error updating account {$accountNumber}: " . $e->getMessage());
                    $errorCount++;
                    
                    Log::error('Error updating account balance via command', [
                        'account_id' => $accountId ?? 'unknown',
                        'activity_id' => $activity->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $this->info("Update completed: {$updatedCount} updated, {$errorCount} errors");

            return 0;

        } catch (\Exception $e) {
            $this->error('Command failed: ' . $e->getMessage());
            Log::error('UpdateAccountBalances command failed: ' . $e->getMessage());
            return 1;
        }
    }
} 