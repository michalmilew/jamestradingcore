<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  ...$guards
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // If user is logged in as regular user
                if ($guard === 'web' || Auth::guard('web')->check()) {
                    return redirect()->route(\App\Models\SettingLocal::getLang().'.client.accounts.index');
                }
                // If user is logged in as admin
                if ($guard === 'admin' || Auth::guard('admin')->check()) {
                    return redirect()->route(\App\Models\SettingLocal::getLang() . '.admin.dashboard');
                }
            }
        }

        return $next($request);
    }
    public function nothandle($request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return $next($request);
            }
        }

        $guard = $guards[0] ?? null;

        switch ($guard) {
            case 'admin':
                $loginPage = 'admin.login';
                break;
            default:
                $loginPage = 'login';
                break;
        }

        return redirect()->route($loginPage);
    }
}
