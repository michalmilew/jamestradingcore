<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradingReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'month',
        'name',
        'broker',
        'login',
        'server',
        'currency',
        'hwm',
        'balance_start',
        'deposit_withdrawal',
        'balance_end',
        'pnl',
        'pnlEUR',
        'performance',
        'accountStatus',
        'accountType'
    ];

    public function userAccount()
    {
        return UserAccount::where('login', $this->login)->first();
    }
}
