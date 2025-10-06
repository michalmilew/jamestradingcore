<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\NotificationRule;
use Illuminate\Support\Facades\Log;
use App\Models\TradingApiReport;

class SendProfitAchievementEmails extends Command
{
    protected $signature = 'email:sendprofit';
    protected $description = 'Send profit achievement notifications to users based on yearly summed PnL';

    public function handle()
    {
        $tradingApiReport = new TradingApiReport;

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

        $modifiedUsers = collect($aggregatedUsers)->filter(function ($item) {
            return str_contains($item['name'], '@');
        });

        $this->info("============== SendProfitAchievementEmails START : {$modifiedUsers->count()} ==============");

        $sent_amount = 0;

        foreach ($modifiedUsers as $apiReport) {
            try {
                $user = User::where('email', $apiReport['name'])->firstOrFail();
                $annualPnL = $apiReport['pnl'];
                $balance = $apiReport['hwm'];

                $this->info("User {$user->email} is sending...");

                if ($user->broker === 'Other') {
                    continue;
                }

                if ($user->email_subscribed) {
                    // Retrieve all notification rules applicable for the user type
                    $rules = NotificationRule::where('type', 'profit')->get();

                    foreach ($rules as $rule) {
                        // Check if the notification should be sent based on the interval
                        if (!$this->shouldSendNotification($rule->interval)) {
                            $this->info("Skipping notification for {$user->email} due to interval restriction ({$rule->interval}).");
                            continue; // Skip this rule if the interval condition is not met
                        }

                        // Map is_vip to corresponding risk levels
                        $userRiskLevel = $this->mapVipToRiskLevel($user->is_vip);

                        // Check if the user's mapped risk level matches the rule
                        if ($rule->risk_level == null || $this->matchesRiskLevel($userRiskLevel, $rule->risk_level)) {
                            // Check min_value, max_value, and risk_level for the rule
                            if (
                                ($rule->min_value === null || $annualPnL >= $rule->min_value) &&
                                ($rule->max_value === null || $annualPnL < $rule->max_value) && $annualPnL > 0
                            ) {

                                $notificationClass = $rule->notification_class;

                                if (class_exists($notificationClass)) {
                                    $notification = new $notificationClass($user->name, ceil($annualPnL), $user->lang);
                                    $notification->sendMail($user);
                                    $this->info("Notification sent to: {$user->email} with annual PnL: {$annualPnL} using {$notificationClass}");
                                    Log::channel('command')->info("email:sendprofit => Notification sent to: {$user->email} with annual PnL: {$annualPnL} using {$notificationClass}");
                                    $sent_amount += 1;
                                } else {
                                    $this->error("Notification class {$notificationClass} does not exist.");
                                }

                                // Break after sending the notification to avoid sending multiple notifications for the same rule
                                break;
                            }
                        }
                    }
                } else {
                    $this->info("User {$user->email} is unsubscribed from email notifications.");
                }

                sleep(5);
            } catch (\Throwable $th) {
                $this->error('ERROR -> ' . $th->getMessage());
            }
        }

        $this->info("============== SendProfitAchievementEmails END : {$sent_amount} ==============");
    }

    /**
     * Map the is_vip value to the corresponding risk level.
     *
     * @param int $isVip
     * @return string
     */
    private function mapVipToRiskLevel($isVip)
    {
        $riskLevels = [
            0 => 'Low',    // or 'Medium' or 'High', depending on your logic
            1 => 'Pro',
            2 => 'Pro+',
            3 => 'Pro++',
            4 => 'Pro+++',
            5 => 'Pro+++', // Same as 4 if needed, or adjust based on actual requirements
        ];

        return $riskLevels[$isVip] ?? 'Low'; // Default to 'Low' if not found
    }

    /**
     * Check if the user's risk level matches the rule's risk level.
     *
     * @param string|null $userRiskLevel
     * @param string|null $ruleRiskLevel
     * @return bool
     */
    private function matchesRiskLevel($userRiskLevel, $ruleRiskLevel)
    {
        if ($ruleRiskLevel === null) {
            return true;
        }

        if ($ruleRiskLevel === 'Low-High') {
            return $userRiskLevel === 'Low' || $userRiskLevel === 'Medium' || $userRiskLevel === 'High';
        } else if ($ruleRiskLevel === 'All') {
            return true;
        }

        return $userRiskLevel === $ruleRiskLevel;
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
