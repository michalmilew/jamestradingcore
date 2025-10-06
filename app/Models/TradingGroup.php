<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradingGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'name',
    ];
    public const RISK_GROUPS = [
        'tXciiLZp' => 'High',   // risk3
        'bXciiLZp' => 'Medium', // risk2
        'aXciiLZp' => 'Low',    // risk1
        'wVZiiLZp' => 'PRO',    // risk4
        'OJKiiLZp' => 'PRO+',   // risk7
        'LJKiiLZp' => 'PRO++',  // risk8
        'ppKiiLZp' => 'PRO+++', // risk9
        'EVZiiLZp' => 'Extra Low',  // risk5
        'LZZiiLZp' => 'Extra High',  // risk6
    ];
    public const LIMITED_RISK_GROUPS = [
        'tXciiLZp' => 'High',
        'bXciiLZp' => 'Medium',
        'aXciiLZp' => 'Low',
        'wVZiiLZp' => 'PRO',
        'OJKiiLZp' => 'PRO+',   // risk7
        'LJKiiLZp' => 'PRO++',  // risk8
        'ppKiiLZp' => 'PRO+++', // risk9
    ];

    public const OTHER_RISK_GROUPS = [
        'EVZiiLZp' => 'Low',
        'LZZiiLZp' => 'High',
    ];

    public static function groupName($group_id , $limited = false,  $other = false){
        if ($other)
            return self::OTHER_RISK_GROUPS[$group_id] ?? "";
        elseif ($limited)
            return self::LIMITED_RISK_GROUPS[$group_id] ?? "";
        else
            return self::RISK_GROUPS[$group_id] ?? "";
    }
}
