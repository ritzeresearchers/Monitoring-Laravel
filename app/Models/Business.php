<?php

namespace App\Models;

use App\Services\Contracts\NotifiableEntity;
use App\Services\Strategy\Jobs\BusinessCancellationStrategy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class Business extends Model implements NotifiableEntity
{
    use HasFactory;

    protected $table = 'businesses';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'lead_location_coverage',
        'logo',
        'name',
        'description',
        'location',
        'address',
        'mobile_number',
        'landline',
        'email',
        'website',
        'is_verified',
        'is_featured',
        'is_active',
        'is_subscription_active',
        'email_verification_code',
        'mobile_number_verification_code',
        'email_verified_at',
        'mobile_number_verified_at'
    ];

    protected $hidden = ['pivot'];

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
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function getIsSubscriptionActiveAttribute()
    {
        return true;
    }

    public function getReviewsAvgRatingAttribute()
    {
        if (!$this->reviews()->count()) {
            return 0;
        }

        return round($this->reviews()->pluck('rating')->sum() / $this->reviews()->count());
    }

    /**
     * @return int
     */
    public function getInProgressJobsCountAttribute(): int
    {
        return $this
            ->jobs()
            ->active()
            ->where('status', config('constants.jobStatus.inProgress'))
            ->count();
    }

    /**
     * @return BelongsToMany
     */
    public function leadLocations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class, 'lead_locations', 'business_id', 'location_id');
    }

    /**
     * @return HasMany
     */
    public function leadCoverage(): HasMany
    {
        return $this->hasMany(LeadLocation::class);
    }

    /**
     * @return BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(WorkCategory::class, 'business_work_category', 'business_id', 'work_category_id');
    }

    /**
     * @return BelongsToMany
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'business_services', 'business_id', 'service_id');
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasOne
     */
    public function bankDetail(): HasOne
    {
        return $this->hasOne(BankDetail::class);
    }

    /**
     * @return HasMany
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * @return HasMany
     */
    public function notificationChannels(): HasMany
    {
        return $this->hasMany(BusinessNotificationChannel::class);
    }

    /**
     * @return HasMany
     */
    public function notifiableEvents(): HasMany
    {
        return $this
            ->hasMany(BusinessNotifiableEvent::class)
            ->limit(count(config('constants.notifiableEvents')));
    }

    /**
     * @return HasMany
     */
    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'hired_business_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class, 'business_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function documents(): HasMany
    {
        return $this->hasMany(BusinessDocument::class, 'business_id', 'id');
    }

    /**
     * @return void
     */
    public static function boot(): void
    {
        parent::boot();

        static::deleting(function (Business $business) {
            foreach ($business->documents as $document) {
                $document->delete();
            }
            foreach ($business->quotes as $quote) {
                $quote->delete();
            }
            foreach ($business->jobs as $job) {
                if ($job->status == config('constants.jobStatus.inProgress')) {
                    $strategy = new BusinessCancellationStrategy();
                    $strategy->cancel($job);
                    $job->update(['status' => 'cancelled']);
                    $job->save();
                }
            }
            foreach ($business->notifiableEvents as $notifiableEvent) {
                $notifiableEvent->delete();
            }
            foreach ($business->notificationChannels as $notificationChannel) {
                $notificationChannel->delete();
            }
            foreach ($business->reviews as $review) {
                $review->delete();
            }

            $business->bankDetail()->delete();
            $business->categories()->detach();
            $business->services()->detach();
        });
    }
}
