<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Referral;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AffiliateController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()
            ->select([
                'users.id',
                'users.name',
                'users.email',
                'users.referral_price',
                DB::raw('COUNT(referrals.id) as referral_count'),
                DB::raw('SUM(CASE WHEN referrals.status = "pending" THEN referrals.amount ELSE 0 END) as payable_balance'),
                DB::raw('SUM(CASE WHEN referrals.status = "paid" THEN referrals.amount ELSE 0 END) as paid_balance')
            ])
            ->leftJoin('referrals', 'users.id', '=', 'referrals.referrer_id')
            ->groupBy('users.id', 'users.name', 'users.email', 'users.referral_price')
            ->having('referral_count', '>', 0);

        // Search functionality
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('users.name', 'LIKE', "%{$search}%")
                  ->orWhere('users.email', 'LIKE', "%{$search}%");
            });
        }

        // Sorting
        $sort = $request->sort ?? 'referral_count';
        $direction = $request->direction ?? 'desc';
        
        // Adjust sorting for aggregated columns
        if (in_array($sort, ['referral_count', 'payable_balance', 'paid_balance'])) {
            $query->orderBy(DB::raw($sort), $direction);
        } else {
            $query->orderBy($sort, $direction);
        }

        $affiliates = $query->paginate(10)->withQueryString();

        return view('admin.affiliates.index', compact('affiliates'));
    }

    public function updatePayment(Request $request, User $user)
    {
        $request->validate([
            'amount_paid' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $pendingReferrals = Referral::where('referrer_id', $user->id)
                                      ->where('status', 'pending')
                                      ->orderBy('created_at')
                                      ->get();

            $amountToPay = $request->amount_paid;
            $remainingAmount = $amountToPay;

            foreach ($pendingReferrals as $referral) {
                if ($remainingAmount <= 0) break;

                $referral->status = 'paid';
                $referral->paid_at = now();
                $referral->save();
                
                $remainingAmount -= $referral->amount;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('Payment updated successfully')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => __('An error occurred while updating payment')
            ], 500);
        }
    }

    public function updateReferralAmount(Request $request, User $user)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Update user's custom referral price
            $user->update([
                'referral_price' => $request->amount
            ]);

            // Update amount for pending referrals
            Referral::where('referrer_id', $user->id)
                   ->where('status', 'pending')
                   ->update(['amount' => $request->amount]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('Referral amount updated successfully')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => __('An error occurred while updating referral amount')
            ], 500);
        }
    }

    public function updateDefaultReferralPrice(Request $request)
    {
        $request->validate([
            'default_price' => 'required|numeric|min:0',
        ]);

        try {
            Setting::where('key', 'default_referral_price')
                  ->update(['value' => $request->default_price]);

            return response()->json([
                'success' => true,
                'message' => __('Default referral price updated successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('An error occurred while updating default referral price')
            ], 500);
        }
    }

    public function getReferrals(User $user)
    {
        try {
            // Log the request
            Log::info('Fetching referrals for user: ' . $user->id);

            $referrals = Referral::where('referrer_id', $user->id)
                               ->orderBy('created_at', 'desc')
                               ->get();

            $pendingAmount = $referrals->where('status', 'pending')->sum('amount');
            $paidAmount = $referrals->where('status', 'paid')->sum('amount');

            // Log the response
            Log::info('Found ' . $referrals->count() . ' referrals for user: ' . $user->id);

            return response()->json([
                'success' => true,
                'referrals' => $referrals,
                'pendingAmount' => $pendingAmount,
                'paidAmount' => $paidAmount
            ]);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error fetching referrals for user ' . $user->id . ': ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => __('An error occurred while fetching referral history')
            ], 500);
        }
    }
} 