<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationChannel extends Model
{
    use HasFactory;

    protected $table = 'notification_channels';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'channel',
        'is_enabled',
    ];
}
