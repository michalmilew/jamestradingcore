<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class UnsubscribeController extends Controller
{
    public function unsubscribe($email)
    {
        $user = User::where('email', $email)->first();

        if ($user) {
            $user->email_subscribed = false;
            $user->save();
            Log::info('UnsubscribeController : User unsubscribed: ' . $email);
        } else {
            Log::error('UnsubscribeController : User not found: ' . $email);
        }
        return redirect('/');
    }
}
