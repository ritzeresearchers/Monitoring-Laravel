<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'locations';

    protected $fillable = [
        'town',
        'location',
        'country_string',
        'eastings',
        'country',
        'region',
        'longitude',
        'uk_region',
        'postcode',
        'latitude',
        'northings',
    ];

}
