<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use App\Models\Settingparam;

use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function setLanguage(Request $request, $lang){

        Session::put('locale', $lang);
        App::setLocale($lang);

        try{
            $user = auth()->user();
            $user->lang = $lang;
            $user->save();
        }catch (\Throwable $th) {
            //throw $th;
        }
        $langs = \App\Models\SettingLocal::getLangsUrl();
        $route =str_replace($langs,'/'.$lang, $request->session()->get('_previous')['url']); 
        //dd( $temp);

        //$sessionData = Session::all();
        //Log::info('coucou  setLanguage ');
        //Log::info($sessionData);
        return redirect($route);
    }
    public function setparams(Request $request){
        $validatedData = $request->validate([
            'email' => 'required|email|max:255',
        ]);

        Settingparam::where('p_key','ADMIN_EMAIL')->update(['p_value' => $request->email]);
        return redirect()->back();
    }
}
