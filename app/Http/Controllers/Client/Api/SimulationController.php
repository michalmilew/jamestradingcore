<?php

namespace App\Http\Controllers\Client\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RiskSetting;
use App\Models\RiskLevel;

class SimulationController extends Controller
{
    public function index(Request $request)
    {
        try {
            $riskSettings = RiskSetting::where('enabled', true)->get();
            
            return response()->json([
                'success' => true,
                'data' => $riskSettings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
} 