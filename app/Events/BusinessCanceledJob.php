<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BusinessCanceledJob
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public int $jobId;
    public string $businessName;
    public User $poster;

    /**
     * @param string $businessName
     * @param User $poster
     */
    public function __construct(int $jobId, string $businessName, User $poster)
    {
        $this->jobId = $jobId;
        $this->businessName = $businessName;
        $this->poster = $poster;
    }

    /**
     * @return PrivateChannel
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('channel-name');
    }
}
