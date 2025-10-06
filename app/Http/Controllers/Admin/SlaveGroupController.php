<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\TradingApiSlaveGroup;

class SlaveGroupController extends Controller
{
    
    public function index(){
        $tradingApiSlaveGroup = new TradingApiSlaveGroup;
        try {
            $settings = $tradingApiSlaveGroup->getSlaveGroupSettings();
            return View('settings.slavegrouplist', compact('settings'));
        } catch (\Exception $e) {
            $settings = [];            
            return View('settings.slavegrouplist', compact('settings'))->with('error', $e->getMessage());
        }        
    }
}
