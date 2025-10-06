<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SettingLocal;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Notifications\ProfitAchievementNotification;
use App\Notifications\ProfitNotificationForFreeProSetting;
use App\Notifications\ProfitNotificationForProSettingPurchase;
use App\Notifications\ProfitNotificationForProSettingDeposit;
use App\Notifications\ProfitNotificationForProPlusSettingDeposit;
use App\Notifications\ProfitNotificationForProPlusPlusSettingDeposit;
use App\Notifications\ProfitNotificationForLowSettingDeposit;
use App\Notifications\ProfitNotificationForMediumSettingDeposit;
use App\Notifications\ProfitNotificationForHighSettingDeposit;
use App\Notifications\ProfitNotificationforProProPlusInvite;
use App\Notifications\NotificationInviteFriend;
use App\Notifications\NotificationMarginRisk;
use App\Models\NotificationRule;
use Illuminate\Support\Facades\Log;

class NotificationTestController extends Controller
{
    public function showForm()
    {
        return view('admin.notification-test');
    }

    public function sendNotification(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'profit' => 'required|numeric',
            'balance' => 'nullable|numeric',
            'margin' => 'nullable|numeric',
            'risk_level' => 'nullable|string',
            'vip_level' => 'nullable|integer',
            'notification_type' => 'required|string',
            'language' => 'required|string|in:en,de,es,fr,it,nl,pt',
        ]);

        try {
            $adminEmail = SettingLocal::getAdminEmail();
            $admin = Admin::where('email', $adminEmail)->firstOrFail();
            $annualPnL = $request->profit;
            $balance = $request->balance;
            $margin = $request->margin;
            $vipLevel = $request->vip_level;
            $riskLevel = $request->risk_level;
            $notificationType = $request->notification_type;
            $language = $request->language;

            switch ($notificationType) {
                case 'ProfitAchievementNotification':
                    $notification = new ProfitAchievementNotification($request->name, $annualPnL, $language);
                    $notification->sendMail($admin);
                    break;

                case 'ProfitNotificationForFreeProSetting':
                    $notification = new ProfitNotificationForFreeProSetting($request->name, $annualPnL, $language);
                    $notification->sendMail($admin);
                    break;

                case 'ProfitNotificationForProSettingPurchase':
                    $notification = new ProfitNotificationForProSettingPurchase($request->name, $annualPnL, $language);
                    $notification->sendMail($admin);
                    break;

                case 'ProfitNotificationForProSettingDeposit':
                    $notification = new ProfitNotificationForProSettingDeposit($request->name, $annualPnL, $language);
                    $notification->sendMail($admin);
                    break;

                case 'ProfitNotificationForProPlusSettingDeposit':
                    $notification = new ProfitNotificationForProPlusSettingDeposit($request->name, $annualPnL, $language);
                    $notification->sendMail($admin);
                    break;

                case 'ProfitNotificationForProPlusPlusSettingDeposit':
                    $notification = new ProfitNotificationForProPlusPlusSettingDeposit($request->name, $annualPnL, $language);
                    $notification->sendMail($admin);
                    break;

                case 'ProfitNotificationForLowSettingDeposit':
                    if ($balance >= 200 && $balance < 1999) {
                        $notification = new ProfitNotificationForLowSettingDeposit($request->name, $language);
                        $notification->sendMail($admin);
                    }
                    break;

                case 'ProfitNotificationForMediumSettingDeposit':
                    if ($balance >= 200 && $balance < 1999) {
                        $notification = new ProfitNotificationForMediumSettingDeposit($request->name, $language);
                        $notification->sendMail($admin);
                    }
                    break;

                case 'ProfitNotificationForHighSettingDeposit':
                    if ($balance >= 200 && $balance < 1999) {
                        $notification = new ProfitNotificationForHighSettingDeposit($request->name, $language);
                        $notification->sendMail($admin);
                    }
                    break;

                case 'ProfitNotificationforProProPlusInvite':
                    $notification = new ProfitNotificationforProProPlusInvite($request->name, $annualPnL, $language);
                    $notification->sendMail($admin);
                    break;

                case 'NotificationInviteFriend':
                    $notification = new NotificationInviteFriend($request->name, $language);
                    $notification->sendMail($admin);
                    break;
                case 'NotificationMarginRisk':
                    $notification = new NotificationMarginRisk($request->name, $language);
                    $notification->sendMail($admin);
                    break;

                default:
                    return redirect()->back()->with('error', 'Invalid notification type selected.');
            }

            return redirect()->back()->with('success', 'Test notification sent to admin successfully!');
        } catch (\Throwable $th) {
            Log::channel('web')->error('NotificationTestController : ' . $th->getMessage());
            return redirect()->back()->with('error', 'An error occurred: ' . $th->getMessage());
        }
    }

    public function sendConditionalNotifications(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'profit' => 'required|numeric',
            'balance' => 'nullable|numeric',
            'margin' => 'nullable|numeric',
            'risk_level' => 'nullable|string',
            'language' => 'required|string|in:en,de,es,fr,it,nl,pt',
        ]);

        try {
            $adminEmail = SettingLocal::getAdminEmail();
            $admin = Admin::where('email', $adminEmail)->firstOrFail();
            $annualPnL = $request->profit;
            $balance = $request->balance;
            $margin = $request->margin;
            $riskLevel = $request->risk_level;
            $language = $request->language;

            $rules = NotificationRule::all();

            foreach ($rules as $rule) {
                // Check if the user's mapped risk level matches the rule
                if ($rule->risk_level == null || $this->matchesRiskLevel($riskLevel, $rule->risk_level)) {
                    // Check min_value, max_value, and risk_level for the rule
                    if (
                        ($rule->min_value === null || $annualPnL >= $rule->min_value) &&
                        ($rule->max_value === null || $annualPnL < $rule->max_value) && ($rule->type !== 'risk')
                    ) {
                        $notificationClass = $rule->notification_class;

                        if (class_exists($notificationClass)) {
                            $notification = new $notificationClass($request->name, ceil($annualPnL), $language);
                            $notification->sendMail($admin);
                        } else {
                            $this->error("Notification class {$notificationClass} does not exist.");
                        }
                    } else if (
                        ($rule->min_value === null || $balance >= $rule->min_value) &&
                        ($rule->max_value === null || $balance < $rule->max_value) && ($rule->type === 'risk')
                    ) {
                        Log::channel('web')->info('NotificationTestController : ' . $rule->type);
                        $notificationClass = $rule->notification_class;

                        if (class_exists($notificationClass)) {
                            $notification = new $notificationClass($request->name, ceil($annualPnL), $language);
                            $notification->sendMail($admin);
                        } else {
                            $this->error("Notification class {$notificationClass} does not exist.");
                        }
                    } else if (
                        ($rule->type === 'margin') && ($margin >= $rule->min_value) && ($margin < $rule->max_value)
                    ) {
                        Log::channel('web')->info('NotificationTestController : ' . $rule->type);
                        $notificationClass = $rule->notification_class;
                        if (class_exists($notificationClass)) {
                            $notification = new $notificationClass($request->name, $language);
                            $notification->sendMail($admin);
                        } else {
                            $this->error("Notification class {$notificationClass} does not exist.");
                        }
                    }
                }
            }

            /*
            // Notifications based on conditions
            if (is_null($vipLevel) || $vipLevel < 1 || $vipLevel > 4) {
                if ($annualPnL >= 10 && $annualPnL < 50) {
                    $admin->notify(new ProfitAchievementNotification($request->name, $annualPnL, $language));
                }
                if ($annualPnL >= 50 && $annualPnL < 100) {
                    $admin->notify(new ProfitNotificationForFreeProSetting($request->name, $annualPnL, $language));
                }
                if ($annualPnL >= 100 && $annualPnL < 1000) {
                    $admin->notify(new ProfitNotificationForProSettingPurchase($request->name, $annualPnL, $language));
                }
            } 
            
            if ($vipLevel >= 1 && $vipLevel <= 3 && $balance >= 200 && $balance < 1999) {
                if ($vipLevel == 1) {
                    $admin->notify(new ProfitNotificationForProSettingDeposit($request->name, $annualPnL, $language));
                } else if ($vipLevel == 2) {
                    $admin->notify(new ProfitNotificationForProPlusSettingDeposit($request->name, $annualPnL, $language));
                } else if ($vipLevel == 3) {
                    $admin->notify(new ProfitNotificationForProPlusPlusSettingDeposit($request->name, $annualPnL, $language));
                }
            }

            if ($balance >= 200 && $balance < 1999) {
                if ($riskLevel == 'Low') {
                    $admin->notify(new ProfitNotificationForLowSettingDeposit($request->name, $language));
                } else if ($riskLevel == 'Medium') {
                    $admin->notify(new ProfitNotificationForMediumSettingDeposit($request->name, $language));
                } else if ($riskLevel == 'High') {
                    $admin->notify(new ProfitNotificationForHighSettingDeposit($request->name, $language));
                }
            }

            if (is_null($vipLevel) || $vipLevel < 1 || $vipLevel > 4) {
                if ($annualPnL >= 50 && $annualPnL < 1999) {
                    // Send notification to the user's email
                    $admin->notify(new ProfitNotificationforProProPlusInvite($annualPnL, $request->name, $language));
                }
            }
            */

            return redirect()->back()->with('success', 'Conditional notifications sent to admin successfully!');
        } catch (\Throwable $th) {
            Log::channel('web')->error('NotificationTestController : ' . $th->getMessage());
            return redirect()->back()->with('error', 'An error occurred: ' . $th->getMessage());
        }
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
}
