<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\App;
use App\Notifications\DummyPnlNotification;

class SendDummyPnlNotification extends Command
{
    protected $signature = 'dummy:send';
    protected $description = 'Send a DummyPnl notification to user with id 1';

    public function handle()
    {
        $user = User::find(1);
        if ($user && $user->email_subscribed) {
            $notification = new DummyPnlNotification($user, $user->lang);
            $notification->sendMail($user);
            $this->info('DummyPnl notification sent to user with id 1');
        } else {
            $this->error('User with id 1 not found');
        }
    }
}
