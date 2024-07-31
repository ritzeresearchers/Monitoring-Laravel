<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Service extends Model
{
    use HasFactory;

    protected $table = 'services';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'work_category_id',
        'is_active',
    ];

    /**
     * @return BelongsToMany
     */
    public function businesses(): BelongsToMany
    {
        return $this->belongsToMany(Business::class, 'business_services', 'service_id', 'business_id');
    }

    public function getActiveBusinessesCountAttribute(): int
    {
        return $this->businesses->where('is_active')->count();
    }

    public function getinactiveBusinessesCountAttribute(): int
    {
        return $this->businesses->where('is_active', false)->count();
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
