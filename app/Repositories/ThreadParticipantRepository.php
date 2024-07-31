<?php

namespace App\Repositories;

use App\Models\ThreadParticipant;

class ThreadParticipantRepository
{
    use Repository;

    protected $model;

    /**
     * @param ThreadParticipant $threadParticipant
     */
    public function __construct(ThreadParticipant $threadParticipant)
    {
        $this->model = $threadParticipant;
    }

    /**
     * @param int $threadId
     * @param int $senderId
     * @return bool
     */
    public function isParticipant(int $threadId, int $senderId): bool
    {
        return (bool)$this->model::where('thread_id', $threadId)
            ->where('participant_id', $senderId)
            ->first();
    }

    /**
     * @param int $threadId
     * @param int $senderId
     * @return string|null
     */
    public function lastRead(int $threadId, int $senderId): ?string
    {
        $participant = $this->model::where('thread_id', $threadId)
            ->where('participant_id', $senderId)
            ->first();

        return $participant ? $participant->last_read : null;
    }

    /**
     * @param int $threadId
     * @param int $participantId
     * @return void
     */
    public function updateParticipantLastReadStatus(int $threadId, int $participantId)
    {
        $this->model::where('thread_id', $threadId)
            ->where('participant_id', $participantId)
            ->first()
            ->update(['last_read' => now()]);

        $this->model::where('thread_id', $threadId)
            ->where('participant_id', '!=', $participantId)
            ->first()
            ->update(['last_read' => null]);
    }
}
