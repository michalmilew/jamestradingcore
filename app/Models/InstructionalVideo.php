<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstructionalVideo extends Model
{
    protected $table = 'instructional_videos';
    protected $fillable = [
        'language',
        'url',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];
} 