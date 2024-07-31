<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bargain extends Model
{
    use HasFactory;

    protected $table = 'bargain';

    protected $fillable = [
        'message_id',
        'quote_id',
        'job_id',
        'user_id',
        'business_id',
        'rate_type',
        'cost',
        'status'
    ];
}
