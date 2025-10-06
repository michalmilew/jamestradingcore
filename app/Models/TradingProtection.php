<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradingProtection extends Model
{
    use HasFactory;
    protected $fillable=[
        'slave_id',  //string 	Slave account_id
        'takeprofit_status', 	//string 	0=disabled, 1=enabled
        'takeprofit_type', 	// string 	Protection type:
                            // 0: CloseOnly
                            // 1: SellOut
                            // 2: Frozen
        'takeprofit_value', // string 	Equity value above which it will trigger the GlobalProtection
        'stoploss_status', 	// string 	0=disabled, 1=enabled
        'stoploss_type', 	// string 	Protection type:
                            // 0: CloseOnly
                            // 1: SellOut
                            // 2: Frozen
        'stoploss_value' 	// string 	Equity value below which it will trigger the GlobalProtection
    ];
    
}
