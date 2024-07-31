<?php

namespace App\Models;

use App\Services\Contracts\NotifiableEntity;
use App\Services\Strategy\Jobs\CustomerCancellationStrategy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Laravel\Cashier\Billable;
class User extends Authenticatable implements JWTSubject,NotifiableEntity
{
    use HasFactory;
    use Notifiable;
    use Billable;


    protected $fillable = [
        'title',
        'first_name',
        'middle_name',
        'last_name',
        'mobile_number',
        'security_code',
        'email',
        'avatar',
        'user_type',
        'password',
        'is_active',
        'verification_code',
        'mobile_number_verification_code',
        'email_verified_at',
        'mobile_number_verified_at',
        'admin_auth_code',
        'stripe_id',
        'pm_type',
        'pm_last_four',
        'trial_ends_at'
    ];

    protected $hidden = [
        'password',
    ];

    protected $appends = ['user_number', 'business_id'];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @return mixed
     */
    public function getUserNameAttribute()
    {
        return $this->isCustomer() ? $this->name : $this->business->name;
    }

    /**
     * @return int|null
     */
    public function getBusinessIdAttribute(): ?int
    {
        return $this->business()->exists() ? $this->business->id : null;
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeCustomer(Builder $query): Builder
    {
        return $query->where('user_type', config('constants.accountType.customer'));
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeBusiness(Builder $query): Builder
    {
        return $query->where('user_type', config('constants.accountType.business'));
    }

    /**
     * @return bool
     */
    public function isCustomer(): bool
    {
        return $this->user_type === config('constants.accountType.customer');
    }

    /**
     * @return bool
     */
    public function isBusiness(): bool
    {
        return $this->user_type === config('constants.accountType.business');
    }

    /**
     * @return HasOne
     */
    public function business(): HasOne
    {
        return $this->hasOne(Business::class, 'user_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function threadParticipants(): HasMany
    {
        return $this->hasMany(ThreadParticipant::class, 'participant_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'poster_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function notificationChannels(): HasMany
    {
        return $this->hasMany(NotificationChannel::class);
    }

    /**
     * @return HasMany
     */
    public function notifiableEvents(): HasMany
    {
        return $this->hasMany(NotifiableEvent::class)->limit(count(config('constants.notifiableEvents')));
    }

    /**
     * @return HasOne
     */
    public function loginToken(): HasOne
    {
        return $this->hasOne(LoginToken::class);
    }

    /**
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    /**
     * @return void
     */
    public static function boot(): void
    {
        parent::boot();

        static::deleting(static function (User $user) {
            foreach ($user->notifiableEvents as $notifiableEvent) {
                $notifiableEvent->delete();
            }
            foreach ($user->notificationChannels as $notificationChannel) {
                $notificationChannel->delete();
            }
            foreach ($user->jobs as $job) {
                if($job->status === config('jobStatus.inProgress')) {
                    $strategy = new CustomerCancellationStrategy();
                    $strategy->cancel($job);
                }

                $job->delete();
            }

            $user->business()->delete();
            $user->loginToken()->delete();
        });
    }
}
