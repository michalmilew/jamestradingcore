<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\LoginNotification;
use Illuminate\Support\Facades\Log;

class SendLoginNotification
{
    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $user = $event->user;

        try {
            Log::info('Sending login notification to ' . $user->email);
            Log::info('User subscribed: ' . $user->email_subscribed);
            Log::info('User language: ' . $user->lang);
            if ($user->email_subscribed) {
                $notification = new LoginNotification($user, $user->lang);
                $notification->sendMail($user);
            }
        } catch (\Exception $e) {
            Log::error('Error sending login notification: ' . $e->getMessage());
        }
    }
}
