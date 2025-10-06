<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function getDefaultReferralPrice()
    {
        return self::where('key', 'default_referral_price')->value('value') ?? 10.00;
    }
} 