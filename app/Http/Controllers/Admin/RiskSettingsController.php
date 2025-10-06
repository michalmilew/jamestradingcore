<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RiskSetting;
use Illuminate\Http\Request;

class RiskSettingsController extends Controller
{
    public function index()
    {
        $riskSettings = RiskSetting::all();
        return view('admin.manage-risk-settings', compact('riskSettings'));
    }

    public function update(Request $request)
    {
        $settings = $request->input('risk_settings');

        foreach ($settings as $key => $setting) {
            $enabled = isset($setting['enabled']) ? true : false; // Ensure it defaults to false if not set
            $multiplier = $setting['multiplier'] ?? 1; // Default to 1 if multiplier is not provided
            $minDeposit = $setting['min_deposit'] ?? 0;

            print_r($setting);

            // Update the risk settings in the database
            RiskSetting::where('name', $key)
                ->update([
                    'enabled' => $enabled,
                    'multiplier' => $multiplier,
                    'min_deposit' => $minDeposit,
                ]);
        }

        return redirect()->back()->with('success', 'Risk settings updated successfully.');
    }

}
