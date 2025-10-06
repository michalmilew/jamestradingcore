<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Exception;

class AccountSuccessfullyConnected extends Notification
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
        $subject = __('notifications.new_account_connected_subject');

        $user = User::where('email', $notifiable->name)->firstOrFail();
        $accountName = parseNameFromEmailUsingNameAPI($user['name']);

        // Prepare the view data for rendering the email content
        $viewData = [
            'greeting' => __('notifications.hello'),
            'name' => $accountName,
            'message' => __('notifications.account_connected_message'),
            'unsubscribe' => config('app.url') . route('unsubscribe', ['email' => $notifiable->name], false),
        ];


        // Render the HTML content using the Blade template
        $htmlContent = View::make('emails.account-successfully-connected', $viewData)->render();

        try {
            // Send the email using the Resend API
            $response = sendResendEmail(
                $notifiable->name,          // Recipient email
                'james@jamestradinggroup.com', // Sender email
                $subject,                    // Subject of the email
                $htmlContent                 // HTML content
            );

            Log::info('Sent AccountSuccessfullyConnected notification to ' . $notifiable->name . ".");

            return $response;
        } catch (Exception $e) {
            // Log the error if email sending fails
            Log::error('Failed to send AccountSuccessfullyConnected notification: ' . $e->getMessage());
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
