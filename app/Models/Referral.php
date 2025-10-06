<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $fillable = [
        'referrer_id',
        'referred_email',
        'amount',
        'status',
        'paid_at'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount' => 'decimal:2'
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($referral) {
            // Get the referrer's custom price or use default
            $referrer = User::find($referral->referrer_id);
            $amount = $referrer->referral_price ?? Setting::getDefaultReferralPrice();
            $referral->amount = $amount;
        });
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referred()
    {
        return $this->belongsTo(User::class, 'referred_id');
    }
} 