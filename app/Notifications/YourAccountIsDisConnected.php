<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Notifications\Notification;
use Exception;

class YourAccountIsDisConnected extends Notification
{
    use Queueable;

    private $account;
    protected $language;

    /**
     * Create a new notification instance.
     *
     * @param array $account
     * @param string|null $language
     */
    public function __construct($account, $language = null)
    {
        $this->account = $account;
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
    public function sendMail($email)
    {
        try {
            // Set the application's locale to the desired language
            $lang = $this->language ?? App::getLocale();
            App::setLocale($lang);

            // Prepare the view data for the Blade template
            $viewData = [
                'greeting' => __('notifications.hello'),
                'message' => __('notifications.view_details'),
                'name' => $this->account['name'],
                'unsubscribe' => config('app.url') . route('unsubscribe', ['email' => $email], false),
            ];

            // Render the HTML content using the Blade template
            $htmlContent = View::make('emails.account-disconnected', $viewData)->render();

            // Email subject
            $subject = __('notifications.account_not_connected_subject', [], $lang);


            // Send the email using the Resend API
            $response = sendResendEmail(
                $email,          // Recipient email
                'james@jamestradinggroup.com',   // Sender email
                $subject,                    // Subject of the email
                $htmlContent                 // HTML content
            );

            Log::info('Sent YourAccountIsDisConnected notification to ' . $email . ".");

            return $response;
        } catch (Exception $e) {
            print_r($e->getMessage());
            // Handle any exceptions, such as logging the error
            Log::error('Failed to send YourAccountIsDisConnected notification: ' . $e->getMessage());
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
