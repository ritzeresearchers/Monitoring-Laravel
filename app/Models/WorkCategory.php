<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkCategory extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'work_categories';

    protected $fillable = [
        'name',
        'is_active',
    ];

    /**
     * @return HasMany
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    /**
     * @return BelongsTo
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'id', 'category_id');
    }

    /**
     * @return BelongsToMany
     */
    public function businesses(): BelongsToMany
    {
        return $this->belongsToMany(Business::class, 'business_work_category', 'work_category_id', 'business_id');
    }

    public function getActiveBusinessesCountAttribute(): int
    {
        return $this->businesses->where('is_active')->count();
    }

    public function getActiveBusinessesNamesAttribute(): array
    {
        return $this->businesses->where('is_active')->pluck('name')->toArray();
    }

    public function getInactiveBusinessesCountAttribute(): int
    {
        return $this->businesses->where('is_active', false)->count();
    }

    public function getInactiveBusinessesNamesAttribute(): array
    {
        return $this->businesses->where('is_active', false)->pluck('name')->toArray();
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
