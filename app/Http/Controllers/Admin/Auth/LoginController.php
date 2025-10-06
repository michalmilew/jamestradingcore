<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Cookie;

class LoginController extends Controller
{

    protected $redirectTo = '/admin/dashboard';

    public function __construct()
    {
        $this->middleware('guest:admin')->except('logout');
    }

    public function showLoginForm()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route(\App\Models\SettingLocal::getLang() . '.admin.dashboard');
        }
        
        if (Auth::guard('web')->check()) {
            return redirect()->route(\App\Models\SettingLocal::getLang() . '.client.accounts.index');
        }
        
        return view('auth.login');
    }

    protected function guard()
    {
        return Auth::guard('admin');
    }

    protected function login(Request $request)
    {
        // Attempt to log in the user
        if (! Auth::guard('admin')->attempt(
            $request->only('email', 'password'),
            true  // Always remember the user
        )) {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // Regenerate session to prevent session fixation
        $request->session()->regenerate();

        // Fetch the logged-in admin user
        $user = Auth::guard('admin')->user();

        // Store user information in session
        session([
            'user_name' => $user->name, 
            'user_role' => $user->role, 
            'user_email' => $user->email,
            'remember_web' => true,
            'admin_session' => true,
            'admin_id' => $user->id
        ]);

        // Set remember me cookie with secure settings
        Cookie::queue('remember_admin', true, 43200, null, null, true, true);

        // Ensure we're using the correct language prefix
        $lang = $user->lang ?? "en";
        return redirect()->route($lang . '.admin.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route(\App\Models\SettingLocal::getLang() . '.admin.dashboard');
    }

    protected function authenticated(Request $request, $user)
    {
        // Set remember me cookie
        $rememberDuration = 43200; // 30 days in minutes
        Cookie::queue('remember_admin', true, $rememberDuration);
    }
}
