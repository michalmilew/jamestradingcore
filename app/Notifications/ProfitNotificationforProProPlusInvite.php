<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Exception;

class ProfitNotificationforProProPlusInvite extends Notification
{
    use Queueable;

    public $profit;
    public $name;
    protected $language;

    public function __construct($name, $profit, $language = null)
    {
        $this->profit = $profit;
        $this->name = $name;
        $this->language = $language;
    }

    public function via($notifiable)
    {
        return [];
    }

    public function toMail($notifiable)
    {
        return null;
    }

    public function sendMail($notifiable)
    {
        // Set the application's locale to the desired language
        App::setLocale($lang = $this->language ?? App::getLocale());

        // Generate the email subject
        $subject = __('notifications.profit_invite_subject');

        // Render the email content from the Blade template
        $viewData = [
            'username' => $this->name,
            'profit' => $this->profit,
            'unsubscribe' => config('app.url') . route('unsubscribe', ['email' => $notifiable->email], false),
        ];

        // Render the HTML content from the Blade view
        $htmlContent = View::make('emails.profit-notification-invite', $viewData)->render();

        try {
            // Send the email using the Resend API
            $response = sendResendEmail(
                $notifiable->email,          // Recipient email
                'james@jamestradinggroup.com', // Sender email
                $subject,                    // Subject of the email
                $htmlContent                 // HTML content
            );

            Log::info('Sent ProfitAchievementNotification to ' . $notifiable->email . ".");

            return $response;
        } catch (Exception $e) {
            // Handle the exception, such as logging the error
            Log::error('Failed to send ProfitAchievementNotification: ' . $e->getMessage());
        }

        return null;
    }

}
