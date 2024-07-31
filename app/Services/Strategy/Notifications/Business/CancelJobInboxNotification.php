<?php

namespace App\Services\Strategy\Notifications\Business;

use App\Events\NewMessage;
use App\Events\NewMessageSent;
use App\Http\Resources\MessageResource;
use App\Models\MessageThread;
use App\Models\User;
use App\Services\Contracts\NotifiableEntity;
use App\Services\Contracts\NotificationStrategy;

class CancelJobInboxNotification implements NotificationStrategy
{
    public function notify($recipient, $data): void
    {
        $messageThread = MessageThread::where('customer_id', $recipient->id)->where('job_id', $data['job_id'])->first();

        $result = new MessageResource($messageThread->messages()->create([
            'message_type' => 'text',
            'text' => $data['message'],
            'sender_id' => $data['senderId'],
            'thread_id' => $messageThread->id
        ]));

        broadcast(new NewMessage($messageThread->id, $result))->toOthers();
        broadcast(new NewMessageSent($data['senderId'], $result))->toOthers();
    }

    public function canNotify(NotifiableEntity $entity, array $settings): bool
    {
        return $settings['notifiableEvents']['messages'] && ($entity instanceof User);
    }
}
