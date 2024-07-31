<?php

namespace App\Models;

use App\Services\Contracts\SMSInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MessageThread extends Model
{
    use HasFactory;

    protected $table = 'message_threads';

    protected $fillable = [
        'job_id',
        'customer_id',
        'quote_id',
        'lead_id',
        'business_id',
        'last_message',
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
    public function quote(): HasOne
    {
        return $this->hasOne(Quote::class, 'id', 'quote_id');
    }

    /**
     * @return HasOne
     */
    public function customer(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'customer_id');
    }

    /**
     * @return HasMany
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'thread_id');
    }

    /**
     * @return HasMany
     */
    public function participants(): HasMany
    {
        return $this->hasMany(ThreadParticipant::class, 'thread_id', 'id');
    }
}
