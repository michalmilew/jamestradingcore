<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RiskSetting;
use App\Models\RiskLevel;

class SimulationController extends Controller
{
    //
    public function __invoke(Request $request){
        $riskSettings = RiskSetting::where('enabled', true)->get();

        return View('client.simulation', compact('riskSettings'));
    }
}
