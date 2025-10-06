<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @method \Laravel\Sanctum\NewAccessToken createToken(string $name, array $abilities = ['*'])
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'email',
        'lang',
        'password',
        'is_vip',
        'notes',
        'ig_user',
        'id_broker',
        'ftd',
        'lots',
        'paid',
        'broker',
        'email_subscribed',
        'restricted_user',
        'inactive',
        'referral_amount',
        'referral_price',
        'last_margin_notification_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'lots' => 'float',
    ];

    protected $dates = [
        'last_margin_notification_at',
    ];

    public function userAccounts()
    {
        return $this->hasMany(UserAccount::class);
    }

    public function telegramGroups()
    {
        return $this->belongsToMany(TelegramGroup::class, 'user_telegram_groups')
                    ->withPivot('invite_link', 'is_used', 'used_at')
                    ->withTimestamps();
    }

    public function forcedTelegramGroups()
    {
        return $this->belongsToMany(TelegramGroup::class, 'forced_telegram_group_access', 'user_id', 'telegram_group_id');
    }

    public static function emailExists($email)
    {
        return self::where('email', $email)->exists();
    }
}
