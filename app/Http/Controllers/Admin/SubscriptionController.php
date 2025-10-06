<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TradingApiSubscription;

class SubscriptionController extends Controller
{
    //
    public function index(Request $request){
        $tradingApiSubscription = new TradingApiSubscription;
        try {
            $subscriptions = $tradingApiSubscription->getSubscriptions();
            return View('subscriptions.list', compact('subscriptions'));
        } catch (\Exception $e) {
            $subscriptions = [];
            return View('subscriptions.list', compact('subscriptions'));
        }catch (\Throwable $th) {
            $subscriptions = [];
            return View('subscriptions.list', compact('subscriptions'));
        }
        
    } 
}
