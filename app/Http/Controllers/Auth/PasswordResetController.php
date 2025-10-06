<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use App\Notifications\CustomResetPasswordNotification; // Import the custom notification

class PasswordResetController extends Controller
{
    /**
     * Display the password reset link request view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        Log::info('========== PasswordResetController ==========');

        // Validate email input
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Find the user by email
        $user = User::where('email', $request->email)->first();

        // Check if the user exists
        if (!$user) {
            Log::error('No user found with email: ' . $request->email);
            return back()->with('error', __('No user found with that email address.'));
        }

        // Generate a password reset token
        $token = Password::createToken($user);

        // Send the custom reset password notification
        $notification = new CustomResetPasswordNotification($token, $user->lang);
        $notification->sendMail($user);

        // Optionally log success
        Log::info('Password reset link sent to: ' . $user->email);

        // Return success message
        return back()->with('status', __('Password reset link sent!'));
    }
}
