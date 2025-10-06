<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TradingApiClient;
use App\Models\User;
use App\Models\NotificationRule;
use Illuminate\Support\Facades\Log;
use App\Models\TradingApiReport;

class SendProfitRiskNotificationEmails extends Command
{
    protected $signature = 'email:sendrisk';
    protected $description = 'Send profit risk notifications to users based on risk level';

    /**
     * Determine the risk level based on the user's account group ID.
     *
     * @param \Illuminate\Support\Collection $accounts
     * @param string $searchEmail
     * @return string
     */
    protected function getRiskLevel($accounts, $searchEmail)
    {
        $foundAccount = $accounts->firstWhere('name', $searchEmail);

        if ($foundAccount) {
            $groupid = $foundAccount['groupid'];

            switch ($groupid) {
                case 'aXciiLZp':
                    return 'Low';
                case 'bXciiLZp':
                    return 'Medium';
                case 'tXciiLZp':
                    return 'High';
                case 'wVZiiLZp':
                    return 'PRO';
                case 'OJKiiLZp':
                    return 'PRO+';
                case 'LJKiiLZp':
                    return 'PRO++';
                case 'ppKiiLZp':
                    return 'PRO+++';
                default:
                    return 'Unknown'; // Handle unexpected groupid values
            }
        } else {
            return 'Not Found'; // Return appropriate value if user is not found
        }
    }

    public function handle()
    {
        $tradingApiClient = new TradingApiClient;
        $accounts = collect($tradingApiClient->getAccounts());

        $tradingApiReport = new TradingApiReport;

        // Retrieve reports for both this year and last year
        $currentYearUsers = $tradingApiReport->getReports(date("Y"));
        $lastYearUsers = $tradingApiReport->getReports(date("Y", strtotime('-1 year')));

        // Merge the reports for both years
        $users = array_merge($currentYearUsers, $lastYearUsers);

        // Initialize an associative array to hold aggregated user data
        $aggregatedUsers = [];

        foreach ($users as $user) {
            $user = (array) $user; // Ensure $user is treated as an array
            $email = $user['name'];

            // Aggregate PnL and highest water mark (HWM) by email
            if (isset($aggregatedUsers[$email])) {
                $aggregatedUsers[$email]['pnl'] += $user['pnl'];
                $aggregatedUsers[$email]['pnlEUR'] += $user['pnlEUR'];
                $aggregatedUsers[$email]['hwm'] = max($aggregatedUsers[$email]['hwm'], $user['hwm']);
            } else {
                $aggregatedUsers[$email] = $user;
            }
        }

        $modifiedUsers = collect($aggregatedUsers)->filter(function($item) {
            return str_contains($item['name'], '@');
        });

        $this->info("============== SendProfitRiskNotificationEmails START : {$modifiedUsers->count()} ==============");

        $sent_amount = 0;

        foreach ($modifiedUsers as $apiReport) {
            try {
                $user = User::where('email', $apiReport['name'])->firstOrFail();
                $balance = $apiReport['hwm']; // Use highest water mark for balance

                if ($user->broker === 'Other') {
                    continue;
                }

                if ($user->email_subscribed) {
                    // Retrieve all notification rules applicable for the risk type
                    $rules = NotificationRule::where('type', 'risk')->get();
                    $risk_level = $this->getRiskLevel($accounts, $user->email);

                    foreach ($rules as $rule) {
                        // Check if the notification should be sent based on the interval
                        if (!$this->shouldSendNotification($rule->interval)) {
                            $this->info("Skipping notification for {$user->email} due to interval restriction ({$rule->interval}).");
                            continue; // Skip this rule if the interval condition is not met
                        }

                        // Check min_value, max_value, and risk_level for the rule
                        if (($rule->min_value === null || $balance >= $rule->min_value) &&
                            ($rule->max_value === null || $balance < $rule->max_value) &&
                            ($rule->risk_level === null || $risk_level == $rule->risk_level)) {

                            $notificationClass = $rule->notification_class;

                            if (class_exists($notificationClass)) {
                                $notification = new $notificationClass($user->name, $user->lang);
                                $notification->sendMail($user);

                                Log::channel('command')->info("email:sendrisk => Notification sent to: {$user->email} with risk level: {$risk_level} using {$notificationClass}");
                                $this->info("Notification sent to: {$user->email} with risk level: {$risk_level} using {$notificationClass}");

                                $sent_amount += 1;
                            } else {
                                $this->error("Notification class {$notificationClass} does not exist.");
                            }

                            // Break after sending the notification to avoid multiple notifications for the same rule
                            break;
                        }
                    }
                } else {
                    $this->info("User {$user->email} is unsubscribed from email notifications.");
                }

                sleep(5); // Optional delay between notifications
            } catch (\Throwable $th) {
                $this->error($th->getMessage());
            }
        }

        $this->info("============== SendProfitRiskNotificationEmails END : {$sent_amount} ==============");
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
