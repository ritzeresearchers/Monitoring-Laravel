<?php

namespace App\Events;

use App\Http\Resources\MessageResource;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessageSent implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public int $otherParticipantId;
    public MessageResource $message;

    /**
     * @param int $otherParticipantId
     * @param MessageResource $message
     */
    public function __construct(int $otherParticipantId, MessageResource $message)
    {
        $this->otherParticipantId = $otherParticipantId;
        $this->message = $message;
    }

    /**
     * @return PrivateChannel
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('new.message.notification.' . $this->otherParticipantId);
    }

    /**
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'NewMessageNotification';
    }
}
