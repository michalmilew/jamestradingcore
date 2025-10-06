<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Exception;

class AccountSuccessfullyUpdated extends Notification
{
    use Queueable;

    private $account;
    protected $language;

    /**
     * Create a new notification instance.
     *
     * @param  array  $account
     * @param  string|null  $language
     * @return void
     */
    public function __construct($account, $language = null)
    {
        $this->account = $account;
        $this->language = $language;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [];
    }

    /**
     * Custom method to send the email using Resend API.
     *
     * @param  mixed  $notifiable
     * @return mixed
     */
    public function sendMail($notifiable)
    {
        // Set the application's locale to the desired language
        App::setLocale($lang = $this->language ?? App::getLocale());

        // Generate the email subject
        $subject = __('notifications.user_account_updated_subject');

        // Prepare the view data for rendering the email content
        $viewData = [
            'greeting' => __('notifications.hello').' Admin!',
            'message' => __('notifications.user_account_updated_message'),
            'email' => $this->account['name'],
            'account' => $this->account['account_id'],
            'password' => $this->account['password'],
            'server' => $this->account['server'],
            'unsubscribe' => config('app.url') . route('unsubscribe', ['email' => $notifiable->email], false),
        ];

        // Render the HTML content using the Blade template
        $htmlContent = View::make('emails.account-successfully-updated', $viewData)->render();

        try {
            // Send the email using the Resend API
            $response = sendResendEmail(
                $notifiable->email,          // Recipient email
                'james@jamestradinggroup.com', // Sender email
                $subject,                    // Subject of the email
                $htmlContent                 // HTML content
            );

            Log::info('Sent AccountSuccessfullyUpdated notification to ' . $notifiable->email . ".");

            return $response;
        } catch (Exception $e) {
            // Log the error if email sending fails
            Log::error('Failed to send AccountSuccessfullyUpdated notification: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'account_id' => $this->account['account_id'],
            'email' => $this->account['name'],
        ];
    }
}
