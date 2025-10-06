<?php

namespace App\Http\Controllers\Client\Api;

use App\Http\Controllers\Controller;
use App\Models\AccountActivity;
use Illuminate\Http\Request;

class AccountActivityController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = AccountActivity::with(['user', 'userAccount'])
                ->where('user_id', auth()->id());

            // Filter by account if provided
            if ($request->filled('account_id')) {
                $query->whereHas('userAccount', function ($q) use ($request) {
                    $q->where('account_id', $request->account_id);
                });
            }

            // Filter by activity type if provided
            if ($request->filled('activity_type')) {
                $query->where('activity_type', $request->activity_type);
            }

            // Get all activities ordered by latest first
            $activities = $query->orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $activities,
                'total' => $activities->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch activities: ' . $e->getMessage()
            ], 500);
        }
    }
}
