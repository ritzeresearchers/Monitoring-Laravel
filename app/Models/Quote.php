<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Quote extends Model
{
    use HasFactory;

    protected $table = 'quotes';

    protected $fillable = [
        'job_id',
        'business_id',
        'rate_type',
        'lead_id',
        'cost',
        'comments',
        'currency',
        'is_accepted',
        'is_not_interested',
        'is_cancelled',
        'status',
        'created_at',
        'updated_at',
    ];

    /**
     * @return HasOne
     */
    public function job(): HasOne
    {
        return $this->hasOne(Job::class, 'id', 'job_id');
    }

    /**
     * @return HasOne
     */
    public function business(): HasOne
    {
        return $this->hasOne(Business::class, 'id', 'business_id');
    }

    /**
     * @return HasOne
     */
    public function bargain(): HasOne
    {
        return $this->hasOne(Bargain::class);
    }

    /**
     * @return HasMany
     */
    public function deadlineAdjustments(): HasMany
    {
        return $this->hasMany(DeadlineAdjustment::class, 'quote_id', 'id');
    }

    /**
     * @return Model|HasMany|object|null
     */
    public function getAcceptedDeadlineAdjustmentAttribute()
    {
        return $this
            ->deadlineAdjustments()
            ->orderBy('created_at', 'desc')
            ->first();
    }

    public function messageThread(): HasOne
    {
        return $this->hasOne(MessageThread::class, 'quote_id', 'id');
    }
}
