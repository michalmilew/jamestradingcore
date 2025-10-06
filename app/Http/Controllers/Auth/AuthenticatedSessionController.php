<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        if (Auth::guard('admin')->check()) {
            $lang = Auth::guard('admin')->user()->lang ?? 'en';
            return redirect()->route($lang . '.admin.dashboard');
        }
        
        if (Auth::guard('web')->check()) {
            $lang = Auth::guard('web')->user()->lang ?? 'en';
            return redirect()->route($lang . '.client.accounts.index');
        }
        
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        $lang = auth()->user()->lang ?? 'en';
        try{
            Session::put('locale', auth()->user()->lang ?? 'en');
        }catch (\Throwable $th) {
            //throw $th;
        }

        return redirect()->route($lang . '.client.accounts.index');
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        $user = auth()->user();
        $lang = $user->lang ?? "en" ;

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect($lang.'/');
    }
}
