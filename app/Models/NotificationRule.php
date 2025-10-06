<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationRule extends Model
{
    protected $fillable = ['name', 'type', 'min_value', 'max_value', 'vip_level', 'risk_level', 'notification_class', 'interval'];
}
