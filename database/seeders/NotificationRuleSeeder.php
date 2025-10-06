<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rules = [
            // Rules for Profit Achievement Emails
            [
                'name' => 'Achievement Profit Notification',
                'type' => 'profit',
                'min_value' => 1000.00,
                'max_value' => 5000.00,
                'vip_level' => 1,
                'notification_class' => 'App\Notifications\ProfitAchievementNotification',
            ],
            [
                'name' => 'Free Pro Setting Profit Notification',
                'type' => 'profit',
                'min_value' => 500.00,
                'max_value' => 1000.00,
                'vip_level' => null,
                'notification_class' => 'App\Notifications\ProfitNotificationForFreeProSetting',
            ],
            [
                'name' => 'Pro Setting Purchase Profit Notification',
                'type' => 'profit',
                'min_value' => 1000.00,
                'max_value' => 3000.00,
                'vip_level' => 2,
                'notification_class' => 'App\Notifications\ProfitNotificationForProSettingPurchase',
            ],
            [
                'name' => 'Pro Setting Deposit Profit Notification',
                'type' => 'profit',
                'min_value' => 3000.00,
                'max_value' => 5000.00,
                'vip_level' => 3,
                'notification_class' => 'App\Notifications\ProfitNotificationForProSettingDeposit',
            ],
            [
                'name' => 'Pro Plus Setting Deposit Profit Notification',
                'type' => 'profit',
                'min_value' => 5000.00,
                'max_value' => 7000.00,
                'vip_level' => 4,
                'notification_class' => 'App\Notifications\ProfitNotificationForProPlusSettingDeposit',
            ],
            [
                'name' => 'Pro Plus Plus Setting Deposit Profit Notification',
                'type' => 'profit',
                'min_value' => 7000.00,
                'max_value' => null,
                'vip_level' => 5,
                'notification_class' => 'App\Notifications\ProfitNotificationForProPlusPlusSettingDeposit',
            ],
            
            // Rule for Profit Invite Emails
            [
                'name' => 'Pro Plus Invite Profit Notification',
                'type' => 'invite',
                'min_value' => 0.00,
                'max_value' => 0.00,
                'vip_level' => null,
                'notification_class' => 'App\Notifications\ProfitNotificationforProProPlusInvite',
            ],
            
            // Rules for Risk Notification Emails
            [
                'name' => 'Low Setting Deposit Risk Notification',
                'type' => 'risk',
                'min_value' => 100.00,
                'max_value' => 500.00,
                'vip_level' => null,
                'notification_class' => 'App\Notifications\ProfitNotificationForLowSettingDeposit',
            ],
            [
                'name' => 'Medium Setting Deposit Risk Notification',
                'type' => 'risk',
                'min_value' => 500.00,
                'max_value' => 1500.00,
                'vip_level' => 2,
                'notification_class' => 'App\Notifications\ProfitNotificationForMediumSettingDeposit',
            ],
            [
                'name' => 'High Setting Deposit Risk Notification',
                'type' => 'risk',
                'min_value' => 1500.00,
                'max_value' => null,
                'vip_level' => 3,
                'notification_class' => 'App\Notifications\ProfitNotificationForHighSettingDeposit',
            ],
        ];

        DB::table('notification_rules')->insert($rules);
    }
}
