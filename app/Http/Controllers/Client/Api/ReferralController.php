<?php

namespace App\Http\Controllers\Client\Api;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Setting;

class ReferralController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();
            
            $referrals = Referral::where('referrer_id', $user->id)->get();
            $defaultReferralPrice = Setting::where('key', 'default_referral_price')->first()->value ?? config('referral.amount', 100);
            
            // Get user's custom referral price if it exists, otherwise use default
            $userReferralPrice = $user->referral_price ?? $defaultReferralPrice;

            $payableBalance = $referrals->where('status', 'pending')->sum('amount');
            $paidBalance = $referrals->where('status', 'paid')->sum('amount');
            $pendingAmount = $payableBalance;
            $totalPaid = $paidBalance;

            return response()->json([
                'success' => true,
                'data' => [
                    'payableBalance' => $payableBalance,
                    'paidBalance' => $paidBalance,
                    'pendingAmount' => $pendingAmount,
                    'totalPaid' => $totalPaid,
                    'referrals' => $referrals,
                    'userReferralPrice' => $userReferralPrice,
                    'defaultReferralPrice' => $defaultReferralPrice
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Referral API Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email'
            ]);

            $user = Auth::user();
            $referredEmail = $request->email;

            // Check if email is the same as the referrer
            if ($user->email === $referredEmail) {
                return response()->json([
                    'success' => false,
                    'message' => __('You cannot refer yourself.')
                ], 400);
            }

            // Check if email has already been used for referral
            if (Referral::where('referred_email', $referredEmail)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => __('This account has already received the referral.')
                ], 400);
            }

            // Find the referred user
            $referredUser = User::where('email', $referredEmail)->first();

            if (!$referredUser) {
                return response()->json([
                    'success' => false,
                    'message' => __('User not found.')
                ], 404);
            }

            // Check if account is less than one week old
            if ($referredUser->created_at->diffInDays(Carbon::now()) > 7) {
                return response()->json([
                    'success' => false,
                    'message' => __('The registration is not recent. Please send a message for verification.')
                ], 400);
            }

            // Get referral amount (either user's custom price or default)
            $referralAmount = $user->referral_price ?? 
                Setting::where('key', 'default_referral_price')->first()->value ?? 
                config('referral.amount', 50);

            // Create referral record
            $referral = Referral::create([
                'referrer_id' => $user->id,
                'referred_email' => $referredEmail,
                'amount' => $referralAmount,
                'status' => 'pending'
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'amount' => $referralAmount,
                    'referred_email' => $referredEmail,
                    'status' => 'pending'
                ],
                'message' => __('The payment has been successfully added to your account.')
            ]);

        } catch (\Exception $e) {
            Log::error('Referral creation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('An error occurred. Please try again later.')
            ], 500);
        }
    }
} 