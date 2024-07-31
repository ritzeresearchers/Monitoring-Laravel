<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessNotifiableEvent extends Model
{
    use HasFactory;

    protected $table = 'business_notifiable_events';

    public $timestamps = false;

    protected $fillable = [
        'business_id',
        'event',
        'is_enabled',
    ];
}
