<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Message extends Model
{
    use HasFactory;

    protected $table = 'messages';

    protected $fillable = [
        'message_type',
        'thread_id',
        'text',
        'media_link',
        'media_name',
        'adjustment_datetime',
        'start_project_option_result',
        'bargain_cost_estimate_result',
        'sender_id',
        'sender_business_id',
    ];

    /**
     * @return HasOne
     */
    public function sender(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'sender_id');
    }

    /**
     * @return HasOne
     */
    public function businessSender(): HasOne
    {
        return $this->hasOne(Business::class, 'id', 'sender_business_id');
    }

    /**
     * @return HasOne
     */
    public function bargain(): HasOne
    {
        return $this->hasOne(Bargain::class, 'message_id', 'id');
    }
}
