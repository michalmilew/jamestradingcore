<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TestProfitNotification;

class TestProfitController extends Controller
{
    /**
     * Display the Test Profit form.
     *
     * @return \Illuminate\View\View
     */
    public function form()
    {
        // Ensure the view path is correct
        return view('admin.test_profit'); // Adjust the view path if necessary
    }

    /**
     * Handle sending the Test Profit email.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function send(Request $request)
    {
        // Validate the request data
        $request->validate([
            'price' => 'required|numeric',
            'pnl' => 'required|numeric',
            'email' => 'required|email',
            'language' => 'required|string|in:en,pt,es,de,nl,it,fr',
        ]);

        try {
            // Extract request data
            $price = $request->input('price');
            $pnl = $request->input('pnl');
            $email = $request->input('email');
            $language = $request->input('language');

            // Send the notification
            Notification::route('mail', $email)
                ->notify(new TestProfitNotification($price, $pnl, $language));

            // Log success
            Log::channel('web')->info('TestProfitController : Test profit email sent successfully to ' . $email);

            return redirect()->back()->with('success', __('Test profit email sent successfully.'));
        } catch (\Throwable $th) {
            // Log the error
            Log::channel('web')->error('TestProfitController : Error sending test profit email: ' . $th->getMessage());
            return redirect()->back()->with('error', __('Failed to send test profit email.'));
        }
    }
}
