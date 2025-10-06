<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cleanup extends Model
{
    protected $fillable = ['min_balance', 'max_balance', 'min_lot_balance', 'disconnect_limit_time', 'cleanup_period', 'inactive_period', 'cleanup_time'];
}