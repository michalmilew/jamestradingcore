<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TradingApiOrder;

class OrderController extends Controller
{
    //
    public function index(Request $request){
        $tradingApiOrder = new TradingApiOrder;
        try {
            $orders = $tradingApiOrder->getOrders();
            return View('orders.list', compact('orders'));
        } catch (\Exception $e) {
            $orders = [];
            return View('orders.list', compact('orders'));
        }catch (\Throwable $th) {
            $orders = [];
            return View('orders.list', compact('orders'));
        }
        
    } 
}
