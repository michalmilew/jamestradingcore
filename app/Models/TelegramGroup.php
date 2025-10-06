<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TelegramGroup extends Model
{
    protected $fillable = [
        'name',
        'key',
        'bot_token',
        'chat_id',
        'min_balance',
        'is_active'
    ];

    protected $casts = [
        'min_balance' => 'float',
        'is_active' => 'boolean'
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_telegram_groups')
            ->withPivot('invite_link', 'is_used', 'used_at')
            ->withTimestamps();
    }
} 