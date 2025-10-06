<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailTest;

use Illuminate\Support\Facades\Log;

class SendTestEmails extends Command
{
    protected $signature = 'email:sendtest';
    protected $description = 'Send Test Email';

    public function handle()
    {
        $recipients = [
            'jamessilva1990410@gmail.com',
            'codingninjaprox@gmail.com'
        ];
        
        foreach ($recipients as $email) {
            Mail::to($email)->send(new EmailTest());
            $this->info("Notification sent to: $email");
        }

        Log::channel('command')->info('SendTestEmails : All notifications sent.');
    }
}


