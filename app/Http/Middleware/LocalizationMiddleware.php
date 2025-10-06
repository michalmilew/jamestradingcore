<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class LocalizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(auth()->user()){
            $lang = auth()->user()->lang ?? 'en';
        }else{
            $lang = Session::get('locale', 'en');
        }        
        
        if(isset($request->segments()[0]))
            $lang = $request->segments()[0];
        //dd($lang);
        
        if(in_array($lang ,array_keys(\App\Models\SettingLocal::getLangs()))){
            App::setLocale($lang);
            if($lang != Session::get('locale', 'en')){
                Session::put('locale', $lang);
            }
        }else
            App::setLocale(Session::get('locale', 'en'));
        
        return $next($request);
    }
}
