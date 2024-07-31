<?php

namespace App\Repositories;

use App\Models\MessageThread;
use App\Models\ThreadParticipant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class MessageThreadRepository
{
    use Repository;

    private int $limit = 30;

    protected $model;

    /**
     * @param MessageThread $messageThread
     */
    public function __construct(MessageThread $messageThread)
    {
        $this->model = $messageThread;
    }

    /**
     * @param string $searchKey
     * @param int $userId
     * @return Builder[]|Collection
     */
    public function search(string $searchKey, int $userId)
    {
        return $this->model::with(['job', 'job.poster', 'participants', 'messages', 'business'])
            ->whereHas('participants', function ($q) use ($userId, $searchKey) {
                $q->where('participant_id', $userId);
            })
            ->whereHas('job', function ($q) use ($searchKey) {
                $q
                    ->where('title', 'LIKE', "%{$searchKey}%")
                    ->orWhere('description', 'LIKE', "%{$searchKey}%")
                    ->orWhereHas('poster', function ($query) use ($searchKey) {
                        $query->where('name', 'LIKE', "%{$searchKey}%");
                    });
            })->orWhereHas('business', function ($q) use ($searchKey) {
                $q
                    ->where('name', 'LIKE', "%{$searchKey}%")
                    ->orWhere('description', 'LIKE', "%{$searchKey}%");
            })
            ->orWhereHas('messages', function ($query) use ($searchKey) {
                    $query->where('text', 'LIKE', "%{$searchKey}%");
            })
            ->get();
    }

    /**
     * @param string $searchKey
     * @param int $userId
     * @return int
     */
    public function countSearch(string $searchKey, int $userId): int
    {
        $threadsIdList = ThreadParticipant::where('participant_id', $userId)
            ->pluck('thread_id')
            ->toArray();

        return $this->model::with('job')
            ->whereIn('id', $threadsIdList)
            ->whereHas('job', function ($q) use ($searchKey) {
                $q->where('title', 'LIKE', "%{$searchKey}%")
                    ->orWhere('description', 'LIKE', "%{$searchKey}%");
            })->orWhereHas('business', function ($q) use ($searchKey) {
                $q->where('name', 'LIKE', "%{$searchKey}%")
                    ->orWhere('description', 'LIKE', "%{$searchKey}%");
            })
            ->with([
                'messages' => function ($query) use ($searchKey) {
                    $query->orWhere('text', 'LIKE', "%{$searchKey}%");
                }
            ])
            ->count();
    }

    /**
     * @param int $threadId
     * @param int $businessId
     * @return Builder|Model|object|null
     */
    public function findByThreadIdAndBusinessId(int $threadId, int $businessId)
    {
        return $this->model::with([
            'business',
            'customer',
            'quote',
            'job'
        ])
            ->where('id', $threadId)
            ->where('business_id', $businessId)
            ->first();
    }

    /**
     * @param int $threadId
     * @param int $userId
     * @return Builder|Model|object|null
     */
    public function findByThreadIdAndUserId(int $threadId, int $userId)
    {
        return $this->model::with([
            'business',
            'customer',
            'quote',
            'job'
        ])
            ->where('id', $threadId)
            ->where('customer_id', $userId)
            ->first();
    }
}
