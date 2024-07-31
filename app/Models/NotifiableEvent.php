<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotifiableEvent extends Model
{
    use HasFactory;

    protected $table = 'notifiable_events';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'event',
        'is_enabled',
    ];
}
