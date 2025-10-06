<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstructionVideo extends Model
{
    protected $fillable = ['language', 'url'];
    
    protected $table = 'instruction_videos';
} 