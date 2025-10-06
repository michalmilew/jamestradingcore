<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SettingLocal extends Model
{
    use HasFactory;

    public static function getLang(){
        if(auth()->user()){
            $lang = auth()->user()->lang ?? 'en';
        }else{
            $lang = Session::get('locale', 'en');
        }   
        return $lang; 
    }

    public static function getLangs(){
        return [
            'pt' => 'Portuguese',
            'en' => 'English',
            'it' => 'Italian',
            'es' => 'Spanish',
            'fr' => 'French',
            'de' => 'German',
            'nl' => 'Dutch',
        ]; 
    }
    public static function getLangsUrl(){
        return [
            '/pt',
            '/en',
            '/it',
            '/es',
            '/fr',
            '/de',
            '/nl',
        ]; 
    }

    public static function getAdminEmail(){
        $adminEmail = Settingparam::where('p_key','ADMIN_EMAIL')->first();
        return $adminEmail != null ? $adminEmail->p_value : 'james@jamestradinggroup.com'; 
    }
}
