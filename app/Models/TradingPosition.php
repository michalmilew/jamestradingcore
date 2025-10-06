<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradingPosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'master_id',
        'ticket',
        'ticketMaster',
        'openTime',
        'side',
        'symbol',
        'openPrice',
        'stopPrice',
        'limitPrice',
        'stopLoss',
        'takeProfit',
        'amountLot',
        'quantityCcy',
        'swapCcy',
        'ccy',
    ];
}
