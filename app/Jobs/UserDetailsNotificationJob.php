<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Notifications\UserDetailsNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

class UserDetailsNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $user;
    private $password;

    public function __construct($user , $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $token = Password::createToken($this->user);                
        $url = url(route($this->user->lang.'.login', false));
        //Notify user
        $notification = new UserDetailsNotification( $url, $this->password, $this->user->lang);
        $notification->sendMail($this->user);
    }
}
