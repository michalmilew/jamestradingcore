<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

class EmailTest extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        App::setLocale('en');
        return $this->subject("Reset Password")
            ->view('emails.new-user-welcome', ['username' => 'Kostiantyn Savelkin', 'profit' => '234', 'unsubscribe' => config('app.url') . route('unsubscribe', ['email' => 'Kostiantynsavelkin@gmail.com'], false)]);
    }
}
