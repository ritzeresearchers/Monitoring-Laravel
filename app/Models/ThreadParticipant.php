<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ThreadParticipant extends Model
{
    use HasFactory;

    protected $table = 'thread_participants';

    protected $fillable = [
        'thread_id',
        'participant_id',
        'last_read',
    ];

    public $timestamps = false;

    public function messageThread(): BelongsTo
    {
        return $this->belongsTo(MessageThread::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'participant_id');
    }
}
