<?php

namespace App\Models;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Job extends Model
{
    use HasFactory;

    protected $table = 'jobs';

    protected $fillable = [
        'email',
        'title',
        'description',
        'poster_id',
        'service_id',
        'category_id',
        'hired_business_id',
        'hired_datetime',
        'is_active',
        'location_id',
        'job_type',
        'pages',
        'amount',
        'paymentstatus',
        'target_job_done',
        'target_completion_datetime',
        'status',
    ];

    protected $appends = ['job_number'];

    /**
     * @return Repository|Application|mixed
     */
    public function getJobNumberAttribute()
    {
        $baseNumber = config('constants.jobBaseNumber');
        return $baseNumber + $this->id;
    }

    public function bargain(): HasOne
    {
        return  $this->hasOne(Bargain::class, 'job_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function quotes(): BelongsTo
    {
        return $this->belongsTo(Quote::class, 'id', 'job_id');
    }

    /**
     * @return HasMany
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'job_id', 'id');
    }

    /**
     * @return HasOne
     */
    public function poster(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'poster_id');
    }

    /**
     * @return HasOne
     */
    public function service(): HasOne
    {
        return $this->hasOne(Service::class, 'id', 'service_id');
    }

    /**
     * @return HasOne
     */
    public function category(): HasOne
    {
        return $this->hasOne(WorkCategory::class, 'id', 'category_id');
    }

    /**
     * @return HasOne
     */
    public function location(): HasOne
    {
        return $this->hasOne(Location::class, 'id', 'location_id');
    }

    /**
     * @return HasOne
     */
    public function lead(): HasOne
    {
        return $this->hasOne(Lead::class);
    }

    /**
     * @return HasOne
     */
    public function hiredBusiness(): HasOne
    {
        return $this->hasOne(Business::class, 'id', 'hired_business_id');
    }

    /**
     * @return Model|BelongsTo|object|null
     */
    public function getAcceptedQuote()
    {
        return $this
            ->quotes()
            ->where('is_accepted', 1)
            ->first();
    }

    /**
     * @param int $pageIndex
     * @return Collection
     */
    public function getPaginatedQuotes(int $pageIndex): Collection
    {
        return $this->quotes()
            ->where('is_cancelled', 0)
            ->limit(config('config.paginationLimit'))
            ->offset(($pageIndex - 1) * config('config.paginationLimit'))
            ->with('business')
            ->get();
    }

    /**
     * @param int $userId
     * @return bool
     */
    public function isPoster(int $userId): bool
    {
        return $this->poster_id === $userId;
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeFinished(Builder $query): Builder
    {
        return $query->where('status', config('constants.jobStatus.finished'));
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeCanceled(Builder $query): Builder
    {
        return $query->where('status', config('constants.jobStatus.cancelled'));
    }
}
