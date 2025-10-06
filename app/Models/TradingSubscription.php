<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TradingSubscription extends Model
{
    
    protected $fillable = [
        'name',
        'expiration_date',
        'type',
        'price',
        'available_accounts',
        'available_slaves',
        'available_masters',
        'equinix'
    ];

    protected $casts = [
        'type' => 'integer',
        'available_accounts' => 'integer',
        'available_slaves' => 'integer',
        'available_masters' => 'integer',
        'equinix' => 'boolean'
    ];
}
