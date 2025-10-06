<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Exception;

class LoginNotification extends Notification
{
    use Queueable;

    protected $user;
    protected $language;

    /**
     * Create a new notification instance.
     *
     * @param object $user
     * @param string|null $language
     */
    public function __construct($user, $language = null)
    {
        $this->user = $user;
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
    public function sendMail($notifiable)
    {
        // Set the application's locale to the desired language
        App::setLocale($lang = $this->language ?? App::getLocale());

        // Generate the unsubscribe URL
        $unsubscribeUrl = config('app.url') . route('unsubscribe', ['email' => $notifiable->email], false);

        // Prepare the view data for the Blade template
        $viewData = [
            'username' => $this->user->name,
            'unsubscribeUrl' => $unsubscribeUrl,
        ];

        // Render the HTML content using the Blade template
        $htmlContent = View::make('emails.login-notification', $viewData)->render();

        // Email subject
        $subject = __('notifications.login_subject');

        try {
            // Send the email using the Resend API
            $response = sendResendEmail(
                $notifiable->email,          // Recipient email
                'james@jamestradinggroup.com', // Sender email
                $subject,                    // Subject of the email
                $htmlContent                 // HTML content
            );

            Log::info('Sent LoginNotification to ' . $notifiable->email . ".");

            return $response;
        } catch (Exception $e) {
            // Handle any exceptions, such as logging the error
            Log::error('Failed to send LoginNotification: ' . $e->getMessage());
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
            'user_id' => $this->user->id,
            'username' => $this->user->name,
        ];
    }
}
