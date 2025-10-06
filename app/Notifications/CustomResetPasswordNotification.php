<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Exception;

class CustomResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;
    public $language;

    public function __construct($token, $language = null)
    {
        $this->token = $token;
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

    public function sendMail($user)
    {
        // Set the application's locale to the desired language
        App::setLocale($lang = $this->language ?? App::getLocale());

        // Generate the email subject
        $subject = __('notifications.password_reset_subject');

        $url = url(route($user->lang.'.password.set', [
            'token' => $this->token,
            'email' => $user->email,
        ], false));

        // Render the email content from the Blade template
        $viewData = [
            'name' =>  $user->name,
            'link' => $url
        ];

        // Render the HTML content from the Blade view
        $htmlContent = View::make('emails.reset-password', $viewData)->render();

        try {
            // Send the email using the Resend API
            $response = sendResendEmail(
                $user->email, // Recipient email
                'james@jamestradinggroup.com', // Sender email
                $subject,                    // Subject of the email
                $htmlContent                 // HTML content
            );

            Log::info('Sent CustomResetPasswordNotification to ' . $user->email . ".");

            return $response;
        } catch (Exception $e) {
            // Handle the exception, such as logging the error
            Log::error('Failed to send CustomResetPasswordNotification: ' . $e->getMessage());
        }

        return null;
    }
}
