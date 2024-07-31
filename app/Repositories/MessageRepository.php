<?php

namespace App\Repositories;

use App\Models\Message;

class MessageRepository
{
    use Repository;

    private int $limit = 30;

    protected $model;

    /**
     * @param Message $message
     */
    public function __construct(Message $message)
    {
        $this->model = $message;
    }

    /**
     * @param int $threadId
     * @param int $pageIndex
     * @return mixed
     */
    public function findByThreadId(int $threadId, int $pageIndex)
    {
        return $this->model::where('thread_id', $threadId)
            ->limit($this->limit)
            ->offset(($pageIndex - 1) * $this->limit)
            ->with('sender')
            ->with('businessSender')
            ->with('bargain')
            ->get();
    }

    /**
     * @param int $threadId
     * @param int $pageIndex
     * @return mixed
     */
    public function findImagesByThreadId(int $threadId, int $pageIndex)
    {
        return $this->model::where('thread_id', $threadId)
            ->where('media_link', '!=', '')
            ->limit($this->limit)
            ->offset(($pageIndex - 1) * $this->limit)
            ->get(['media_link'])
            ->pluck(['media_link'])
            ->toArray();
    }
}
