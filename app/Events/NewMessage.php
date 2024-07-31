<?php

namespace App\Events;

use App\Http\Resources\MessageResource;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessage implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public int $threadId;
    public MessageResource $message;

    /**
     * @param int $threadId
     * @param MessageResource $chatMessage
     */
    public function __construct(int $threadId, MessageResource $chatMessage)
    {
        $this->threadId = $threadId;
        $this->message = $chatMessage;
    }

    /**
     * @return PrivateChannel
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('threadId.' . $this->threadId);
    }

    /**
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'NewMessage';
    }
}
