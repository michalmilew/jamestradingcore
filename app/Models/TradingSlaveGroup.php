<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradingSlaveGroup extends Model
{
    use HasFactory;
    protected $fillable = [
        'slave_id',
        'group_id',
        'master_id',
        'risk_factor_value',
        'risk_factor_type',
        'max_order_size',
        'copier_status'
    ];

    protected $appends = [
        'risk_factor_type_name',
        'copier_status_name',
    ];

    public const RISK_FACTOR_TYPES = [
        0 => 'Auto Risk (Equity)',
        1 => 'Auto Risk (Balance)',
        2 => 'Auto Risk (Free Margin)',
        3 => 'Multiplier (Notional)',
        11 => 'Multiplier (Lot)',
        4 => 'Fixed Lot',
        10 => 'Fixed Units',
        5 => 'Fixed Leverage (Equity)',
        6 => 'Fixed Leverage (Balance)',
        7 => 'Fixed Leverage (Free Margin)',
    ];

    public const COPIER_STATUSES = [
        '-1' => 'Close Only',
        '0' => 'Frozen',
        '1' => 'On',
        '2' => 'Open Only',
    ];

    public function getRiskFactorTypeNameAttribute()
    {
        return self::RISK_FACTOR_TYPES[$this->risk_factor_type] ?? null;
    }

    public function getCopierStatusNameAttribute()
    {
        return self::COPIER_STATUSES[$this->copier_status] ?? null;
    }
}
