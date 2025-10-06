<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Models\NotificationLanguageKey;

class NotificationController extends Controller
{
    /**
     * Display the form to select a notification type and language.
     */
    public function selectNotification()
    {
        // Retrieve available notification types and languages
        $notificationTypes = [
            'ProfitAchievementNotification',
            'ProfitNotificationForFreeProSetting',
            'ProfitNotificationForHighSettingDeposit',
            'ProfitNotificationForLowSettingDeposit',
            'ProfitNotificationForMediumSettingDeposit',
            'ProfitNotificationForProPlusPlusSettingDeposit',
            'ProfitNotificationForProPlusSettingDeposit',
            'ProfitNotificationforProProPlusInvite',
            'NotificationInviteFriend',
            'NotificationMarginRisk',
            'ProfitNotificationForProSettingDeposit',
            'ProfitNotificationForProSettingPurchase',
            'CustomResetPasswordNotification',
            'AccountSuccessfullyAdded',
            'AccountSuccessfullyConnected',
            'AccountSuccessfullyUpdated',
            'AccountWasDeleted',
            'CleanupNotification',
            'CleanupForUserNotification',
            'LoginNotification',
            'UserCreatedNotification',
            'UserDetailsNotification',
            'YourAccountIsDisConnected',
            'AccountSuccessfullyConnected'
        ];

        $languages = ['en', 'de', 'es', 'fr', 'it', 'nl', 'pt']; // Add more languages if needed

        return view('admin.notifications.select', compact('notificationTypes', 'languages'));
    }

    /**
     * Handle the form submission and redirect to the edit page.
     */
    public function redirectToEdit(Request $request)
    {
        $notificationType = $request->input('notification_type');
        $language = $request->input('language');

        return redirect()->route(\App\Models\SettingLocal::getLang() . '.admin.notifications.edit', [$notificationType, $language]);
    }

    /**
     * Show the form for editing the specified notification's language variables.
     */
    public function edit($notificationType, $language)
    {
        $filePath = resource_path("lang/{$language}/notifications.php");

        // Check if the language file exists
        if (!File::exists($filePath)) {
            abort(404, "Language file not found.");
        }

        // Get the language variables from the file
        $allTranslations = include($filePath);

        // Retrieve the relevant variable keys from the NotificationLanguageKey model
        $registeredKeys = NotificationLanguageKey::where('notification_type', $notificationType)
            ->pluck('language_key')
            ->toArray();

        // Filter translations based on the registered keys
        $translations = [];
        foreach ($allTranslations as $key => $value) {
            if (in_array($key, $registeredKeys)) {
                $translations[$key] = $value;
            }
        }

        if (empty($translations)) {
            abort(404, "No relevant translations found for the selected notification type.");
        }

        return view('admin.notifications.edit', compact('notificationType', 'language', 'translations'));
    }


    /**
     * Update the specified notification's language variables.
     */
    public function update(Request $request, $notificationType, $language)
    {
        $filePath = resource_path("lang/{$language}/notifications.php");

        // Check if the language file exists
        if (!File::exists($filePath)) {
            abort(404, "Language file not found.");
        }

        // Get the current translations from the file
        $translations = include($filePath);

        // Update the relevant translations
        foreach ($request->input('translations') as $key => $value) {
            if (array_key_exists($key, $translations)) {
                $translations[$key] = $value;
            }
        }

        // Save the updated translations back to the file
        File::put($filePath, '<?php return ' . var_export($translations, true) . ';');

        return redirect()->route(\App\Models\SettingLocal::getLang() . '.admin.notifications.edit', [$notificationType, $language])
            ->with('status', 'Translations updated successfully!');
    }
}
