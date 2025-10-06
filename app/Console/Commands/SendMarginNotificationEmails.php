<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TradingApiClient;
use App\Models\User;
use App\Models\NotificationRule;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendMarginNotificationEmails extends Command
{
    protected $signature = 'email:sendmargin';
    protected $description = 'Send margin notification to users who has low margin balance';

    public function handle()
    {
        $this->info('"==============  Start SendMarginNotificationEmails "==============');
        $this->info('email:sendmargin command executed at ' . now());

        $tradingApiClient = new TradingApiClient;
        $accounts = $tradingApiClient->getAccounts();
        $accounts = collect($accounts);

        $this->info('Total Trading users num: ' . count($accounts));

        foreach ($accounts as $account) {
            try {
                if ($account['balance'] > 0) {
                    $percent = $account['free_margin'] / $account['balance'] * 100;
                    
                    // Changed from firstOrFail() to first() to avoid exception
                    $user = User::where('email', $account['name'])->first();
                    
                    // Skip if user not found
                    if (!$user) {
                        Log::channel('command')->warning("email:sendmargin => User not found for account: {$account['name']}");
                        $this->warn("User not found for account: {$account['name']}");
                        continue;
                    }

                    if ($user->broker === 'Other') {
                        continue;
                    }

                    // Check if 24 hours have passed since the last notification
                    if ($user->last_margin_notification_at && 
                        Carbon::parse($user->last_margin_notification_at)->addHours(24)->isFuture()) {
                        $this->info("Skipping notification for {$user->email} - Less than 24 hours since last notification");
                        continue;
                    }

                    if ($user->email_subscribed) {
                        // Retrieve all notification rules applicable for the invite type
                        $rules = NotificationRule::where('type', 'margin')->get();

                        foreach ($rules as $rule) {
                            // Check if the notification should be sent based on the interval
                            if (!$this->shouldSendNotification($rule->interval)) {
                                continue;
                            }

                            if ($percent <= $rule->max_value && $percent > $rule->min_value && $rule->type === 'margin') {
                                $this->warn('Email : ' . $account['name'] . ' Name: ' . $account['account'] . ' Percent : ' . $percent . "%.");
                            } else {
                                continue;
                            }
                            
                            $notificationClass = $rule->notification_class;

                            if (class_exists($notificationClass)) {
                                try {
                                    $notification = new $notificationClass($account['account'], $user->lang);
                                    $notification->sendMail($user);
                                    
                                    // Update the last notification timestamp
                                    $user->update([
                                        'last_margin_notification_at' => Carbon::now()
                                    ]);

                                    Log::channel('command')->info("email:sendmargin => Notification sent to: {$user->email} {$account['account']} with percent: {$percent} using {$notificationClass}");
                                    $this->info("Notification sent to: {$user->email} {$account['account']} with percent: {$percent} using {$notificationClass}");
                                } catch (\Exception $e) {
                                    Log::channel('command')->error("email:sendmargin => Error sending notification: " . $e->getMessage());
                                    $this->error("Error sending notification: " . $e->getMessage());
                                }
                            } else {
                                Log::channel('command')->error("email:sendmargin => Notification class {$notificationClass} does not exist.");
                                $this->error("Notification class {$notificationClass} does not exist.");
                            }

                            break;
                        }
                    } else {
                        $this->info("User {$user->email} is unsubscribed from email notifications.");
                    }
                }
            } catch (\Exception $e) {
                Log::channel('command')->error("email:sendmargin => Error processing account: " . $e->getMessage());
                $this->error("Error processing account: " . $e->getMessage());
                continue;
            }
        }

        $this->info("============== SendMarginNotificationEmails END ==============");
    }

    /**
     * Determine if the notification should be sent based on the current date and rule's interval.
     *
     * @param string $interval
     * @return bool
     */
    private function shouldSendNotification($interval)
    {
        $today = date('l'); // Get the current day of the week, e.g., 'Monday', 'Tuesday', etc.

        switch ($interval) {
            case 'Daily':
                return true; // Always send for daily notifications
            case 'Weekly':
                return date('N') === '1'; // Send only on Mondays (ISO-8601 numeric representation, Monday = 1)
            case 'Monthly':
                return date('j') === '1'; // Send only on the 1st of every month
            default:
                return $today === $interval; // Send only on the specific day (e.g., 'Monday')
        }
    }
}
