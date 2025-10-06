<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradingSymbolSetting extends Model
{
    use HasFactory;
    protected $fillable = [
        'slave_id',
        'group_id',
        'master_id',
        'symbol',
        'symbol_master'
    ];
}
