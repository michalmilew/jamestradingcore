<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Models\Setting;

class NotificationInviteFriend extends Notification
{
    use Queueable;
    public $name;
    protected $language;
    protected $referralPrice;

    public function __construct($name, $language = null)
    {
        $this->name = $name;
        $this->language = $language;
        $this->referralPrice = Setting::where('key', 'default_referral_price')->first()->value ?? config('referral.amount', 100);
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

        // Generate the email subject with dynamic referral price
        $subject = __('notifications.invite_friend_subject', ['amount' => $this->referralPrice]);

        // Render the email content from the Blade template
        $viewData = [
            'username' => $this->name,
            'unsubscribe' => config('app.url') . route('unsubscribe', ['email' => $notifiable->email], false),
            'referralPrice' => $this->referralPrice
        ];

        // Render the HTML content from the Blade view
        $htmlContent = View::make('emails.invite_friend', $viewData)->render();

        try {
            // Send the email using the Resend API
            $response = sendResendEmail(
                $notifiable->email,          // Recipient email
                'james@jamestradinggroup.com', // Sender email
                $subject,                    // Subject of the email
                $htmlContent                 // HTML content
            );

            Log::info('Sent NotificationInviteFriend to ' . $notifiable->email . ".");

            return $response;
        } catch (Exception $e) {
            // Handle the exception, such as logging the error
            Log::error('Failed to send NotificationInviteFriend: ' . $e->getMessage());
        }

        return null;
    }
}
