<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankDetail extends Model
{
    use HasFactory;

    protected $table = 'bank_details';

    protected $fillable = [
        'business_id',
        'account_holder_name',
        'account_number',
        'bank_sort_code',
        'post_code',
        'line1',
        'line2',
    ];

    public $timestamps = false;
}
