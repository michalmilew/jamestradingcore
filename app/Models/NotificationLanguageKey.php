<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationLanguageKey extends Model
{
    protected $fillable = ['notification_type', 'language_key'];
}
