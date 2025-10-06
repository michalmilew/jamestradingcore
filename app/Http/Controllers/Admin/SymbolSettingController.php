<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\TradingApiSymbolSetting;

class SymbolSettingController extends Controller
{
    //
    public function index(){
        $tradingApiSymbolSetting = new TradingApiSymbolSetting;
        try {
            $sttings =  $tradingApiSymbolSetting->getSymbolSettings();

            return View('settings.symbolsettingslist', compact('settings'));
        } catch (\Exception $e) {
            $settings = [];
            return View('settings.symbolsettingslist', compact('settings'))->with('error', $e->getMessage());
        }
    }
}
