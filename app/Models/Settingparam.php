<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settingparam extends Model
{
    use HasFactory;
    protected $table = 'settingparams';
    protected $fillable = [
        'p_key',
        'p_value',
    ];
}
