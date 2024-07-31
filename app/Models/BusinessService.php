<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessService extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'business_services';

    protected $fillable = [
        'business_id',
        'service_id',
    ];
}
