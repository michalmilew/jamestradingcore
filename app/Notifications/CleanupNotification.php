<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Exception;

class CleanupNotification extends Notification
{
    use Queueable;

    protected $minumun_balance;
    protected $max_balance;
    protected $accountId;
    protected $accountName;
    protected $balance;
    protected $language;

    /**
     * Create a new notification instance.
     *
     * @param float $minumun_balance
     * @param float $max_balance
     * @param string $accountId
     * @param string $accountName
     * @param float $balance
     * @param string|null $language
     */
    public function __construct($minumun_balance, $max_balance, $accountId, $accountName, $balance, $language = null)
    {
        $this->minumun_balance = $minumun_balance;
        $this->max_balance = $max_balance;
        $this->accountId = $accountId;
        $this->accountName = $accountName;
        $this->balance = $balance;
        $this->language = $language;
    }

    /**
     * Determine the channels the notification should be sent on.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [];
    }

    /**
     * Custom method to send the email using the Resend API.
     *
     * @param  mixed  $notifiable
     * @return mixed
     */
    public function sendMail($notifiable)
    {
        // Set the application's locale to the desired language
        App::setLocale($lang = $this->language ?? App::getLocale());

        // Generate the email subject
        $subject = __('notifications.clean_subject');

        // Prepare the view data for rendering the email content
        $viewData = [
            'minumun_balance' => $this->minumun_balance,
            'max_balance' => $this->max_balance,
            'balance' => $this->balance,
            'accountId' => $this->accountId,
            'accountName' => $this->accountName,
        ];

        // Render the HTML content using the Blade template
        $htmlContent = View::make('emails.cleanup-email', $viewData)->render();

        try {
            // Send the email using the Resend API
            $response = sendResendEmail(
                $notifiable->email,          // Recipient email
                'james@jamestradinggroup.com', // Sender email
                $subject,                    // Subject of the email
                $htmlContent                 // HTML content
            );

            Log::info('Sent CleanupNotification to ' . $notifiable->email . ".");

            return $response;
        } catch (Exception $e) {
            // Log the error if email sending fails
            Log::error('Failed to send CleanupNotification: ' . $e->getMessage());
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
            'account_id' => $this->accountId,
            'account_name' => $this->accountName,
            'balance' => $this->balance,
            'minumun_balance' => $this->minumun_balance,
            'max_balance' => $this->max_balance,
        ];
    }
}
