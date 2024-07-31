<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessNotificationChannel extends Model
{
    use HasFactory;

    protected $table = 'business_notification_channels';

    public $timestamps = false;

    protected $fillable = [
        'business_id',
        'channel',
        'is_enabled',
    ];
}
