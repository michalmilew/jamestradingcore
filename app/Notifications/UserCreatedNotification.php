<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Exception;

class UserCreatedNotification extends Notification
{
    use Queueable;

    private $url;
    protected $language;

    /**
     * Create a new notification instance.
     *
     * @param string $url
     * @param string|null $language
     */
    public function __construct($url, $language = null)
    {
        $this->url = $url;
        $this->language = $language;
    }

    /**
     * Get the channels the notification should be delivered on.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [];
    }

    /**
     * Custom method to send the email using the Resend API.
     *
     * @param mixed $notifiable
     * @return mixed
     */
    public function sendMail($user)
    {
        // Set the application's locale to the desired language
        App::setLocale($lang = $this->language ?? App::getLocale());

        // Prepare the view data for the Blade template
        $viewData = [
            'name' => $user->name,
            'app_name' => 'James Trading Group',
            'url' => $this->url,
        ];

        Log::info('UserCreatedNotification viewData : ' . $user->name);

        // Render the HTML content using the Blade template
        $htmlContent = View::make('emails.user-created-notification', $viewData)->render();

        // Email subject
        $subject = __('notifications.welcome_subject', ['app_name' => env('APP_NAME')]);

        try {
            // Send the email using the Resend API
            $response = sendResendEmail(
                $user->email,          // Recipient email
                'james@jamestradinggroup.com',   // Sender email
                $subject,                    // Subject of the email
                $htmlContent                 // HTML content
            );

            Log::info('Sent UserCreatedNotification to ' . $user->email . ".");

            return $response;
        } catch (Exception $e) {
            // Handle any exceptions, such as logging the error
            Log::error('Failed to send UserCreatedNotification: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            // No additional data for array representation
        ];
    }
}
