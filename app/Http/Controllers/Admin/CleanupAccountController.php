<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cleanup;
use Illuminate\Support\Facades\Artisan;

class CleanupAccountController extends Controller
{
    public function index(){
        // Get the first (or create a new) cleanup settings record
        $cleanup = Cleanup::firstOrCreate(
            ['id' => 1], // Assuming only one record should exist
            ['min_balance' => 0.01, 'max_balance' => 10, 'min_lot_balance' => 10, 'disconnect_limit_time' => 15, 'cleanup_period' => 7, 'inactive_period' => 15]
        );

        return view('cleanup.config', compact('cleanup'));
    }

    public function update(Request $request)
    {
        $request->merge([
            'cleanup_time' => date("H:i", strtotime($request->cleanup_time))
        ]);
        
        // Validate input
        $request->validate([
            'min_balance' => 'numeric|min:0',
            'max_balance' => 'numeric|min:0',
            'min_lot_balance' => 'numeric|min:0',
            'disconnect_limit_time' => 'integer|min:1',
            'cleanup_period' => 'integer|min:1',
            'inactive_period' => 'integer|min:1',
            'cleanup_time' => 'required|date_format:H:i'
        ]);

        // Update the cleanup settings
        $cleanup = Cleanup::first();
        $cleanup->update($request->all());

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }

    public function execute(Request $request)
    {
        // Dispatch the Artisan command
        Artisan::call('accounts:cleanup');
        
        // Redirect back with a success message
        return redirect()->back()->with('success', 'Cleanup process has been initiated.');
    }
}
