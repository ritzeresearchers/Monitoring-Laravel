<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadLocation extends Model
{
    use HasFactory;

    protected $table = 'lead_locations';

    public $with = ['location'];

    protected $fillable = [
        'location_type',
        'business_id',
        'location_id',
        'radius',
        'longitude',
        'latitude',
    ];

    protected $hidden = ['pivot'];

    public $timestamps = false;

    /**
     * @return BelongsTo
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
