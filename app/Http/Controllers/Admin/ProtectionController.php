<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\TradingApiProtection;

class ProtectionController extends Controller
{
    public function index(){
        $tradingApiProtection = new TradingApiProtection;

        try {
            $protections = $tradingApiProtection->getProtections();
            return View('settings.protections', compact('protections'));
        } catch (\Exception $e) {
            $protections = [];
            return View('settings.protections', compact('protections'))->with('error', $e->getMessage());
        }
    }

}
