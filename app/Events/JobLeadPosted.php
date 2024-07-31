<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class JobLeadPosted implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public int $otherUserId;
    public int $businessId;
    public array $lead;

    /**
     * @param int $otherUserId
     * @param int $businessId
     * @param array $lead
     */
    public function __construct(int $otherUserId, int $businessId, array $lead)
    {
        $this->otherUserId = $otherUserId;
        $this->businessId = $businessId;
        $this->lead = $lead;
    }

    /**
     * @return PrivateChannel
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('new.job.lead.notification.' . $this->businessId);
    }

    /**
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'NewJobLeadNotification';
    }
}
