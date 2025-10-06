<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Notifications\Notification;
use Exception;

class UserDetailsNotification extends Notification
{
    use Queueable;

    private $url;
    private $password;
    protected $language;

    /**
     * Create a new notification instance.
     *
     * @param string $url
     * @param string $password
     * @param string|null $language
     */
    public function __construct($url, $password, $language = null)
    {
        $this->url = $url;
        $this->password = $password;
        $this->language = $language;
    }

    /**
     * Get the channels the notification should be delivered on.
     *
     * @param mixed $user
     * @return array
     */
    public function via($notifiable)
    {
        return [];
    }

    /**
     * Custom method to send the email using the Resend API.
     *
     * @param mixed $user
     * @return mixed
     */
    public function sendMail($user)
    {
        // Set the application's locale to the desired language
        App::setLocale($lang = $this->language ?? App::getLocale());

        // Prepare the view data for the Blade template
        $viewData = [
            'email' => $user->email,
            'password' => $this->password,
            'loginUrl' => $this->url,
        ];

        // Render the HTML content using the Blade template
        $htmlContent = View::make('emails.user-details-notification', $viewData)->render();

        // Email subject
        $subject = __('notifications.account_details_subject');

        try {
            // Send the email using the Resend API
            $response = sendResendEmail(
                $user->email,          // Recipient email
                'james@jamestradinggroup.com',   // Sender email
                $subject,                    // Subject of the email
                $htmlContent                 // HTML content
            );

            Log::info('Sent UserDetailsNotification to ' . $user->email . ".");

            return $response;
        } catch (Exception $e) {
            // Handle any exceptions, such as logging the error
            Log::error('Failed to send UserDetailsNotification: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $user
     * @return array
     */
    public function toArray($user)
    {
        return [
            // No additional data for array representation
        ];
    }
}
