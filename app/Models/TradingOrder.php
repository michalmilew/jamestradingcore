<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradingOrder extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'timestamp',
        'master_id',
        'ticketMaster',
        'account_id',
        'side',
        'action',
        'symbol',
        'quantityOrder',
        'stopLoss',
        'takeProfit',
        'quantityExecuted',
        'quantityExecutedEUR',
        'quantityExecutedUSD',
        'priceExecuted',
        'status_id',
        'statusName',
    ];
}
