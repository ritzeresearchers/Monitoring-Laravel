<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeadlineAdjustment extends Model
{
    use HasFactory;

    protected $table = 'deadline_adjustments';

    protected $fillable = [
        'quote_id',
        'job_id',
        'sender_business_id',
        'sender_id',
        'adjustment_datetime',
        'message_id',
        'status',
    ];
}
