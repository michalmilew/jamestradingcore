<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TestProfitNotification extends Notification
{
    use Queueable;

    protected $price;
    protected $pnl;
    protected $language;

    /**
     * Create a new notification instance.
     *
     * @param float $price
     * @param float $pnl
     * @param string $language
     */
    public function __construct($price, $pnl, $language)
    {
        $this->price = $price;
        $this->pnl = $pnl;
        $this->language = $language;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        // Set the locale to the specified language
        app()->setLocale($this->language);

        return (new MailMessage)
            ->subject(__('notifications.test_profit_email_subject'))
            ->greeting(__('notifications.greeting'))
            ->line(__('notifications.here_is_your_test_profit_email'))
            ->line(__('notifications.price_label', ['price' => $this->price]))
            ->line(__('notifications.pnl_label', ['pnl' => $this->pnl]))
            ->action(__('notifications.view_details'), url('/'))
            ->line(__('notifications.thank_you_message'))
            ->salutation(__('notifications.best_regards'));
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
            'price' => $this->price,
            'pnl' => $this->pnl,
        ];
    }
}
