<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\TradingApiPosition;
use App\Models\TradingPosition;

class PositionController extends Controller
{
    
    public function getOpenPositions(Request $request){
        
        $title =  'Open Positions list';
        try {
            $tradingApiPosition = new TradingApiPosition;
            $positions = $tradingApiPosition->getPositions('getOpenPositions');
            return View('positions.list', compact('positions', 'title')); 
        } catch (\Exception $e) {
            $positions = [];
            return View('positions.list', compact('positions', 'title'))->with('error', $e->getMessage() ); 
        }    
    }

    public function getClosedPositions(Request $request){
        $title =  'Closed Positions list';
        try {
            $tradingApiPosition = new TradingApiPosition;
            $positions = $tradingApiPosition->getPositions('getClosedPositions');
            return View('positions.list', compact('positions', 'title'));
        } catch (\Exception $e) {
            $positions = [];            
            $error = $e->getMessage();
            return View('positions.list', compact('positions', 'title','error')); 
        } 
    }
}



