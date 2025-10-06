<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAccount extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'user_id',
        'account_id',
        'login',
        'is_connected',
        'last_known_state',
        'disconnected_at',
        'server',
        'platform_type',
        'balance',
        'equity',
        'template_id',
        'initial_lot',
        'max_pairs',
        'active_pairs'
    ];

    protected $casts = [
        'is_connected' => 'boolean',
        'disconnected_at' => 'datetime',
        'balance' => 'decimal:2',
        'equity' => 'decimal:2',
        'initial_lot' => 'decimal:2',
        'active_pairs' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function accountActivity()
    {
        return $this->hasMany(AccountActivity::class);
    }

    // Template relationship will be added when Template model is created
    // public function template()
    // {
    //     return $this->belongsTo(Template::class);
    // }
}
