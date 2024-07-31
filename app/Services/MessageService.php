<?php

namespace App\Services;

use App\Models\Message;
use App\Models\Job;
use App\Models\MessageThread;
use App\Models\ThreadParticipant;
use Exception;
use function config;

class MessageService
{
    private $messageThread;

    /**
     * @throws Exception
     */
    public function createThread($messageThreadPayload)
    {
        try {
            $job = Job::find($messageThreadPayload['job_id']);
            $this->messageThread = MessageThread::where('job_id', $job->id)
                ->where('lead_id', $messageThreadPayload['lead_id'])
                ->first();

            if (!$this->messageThread) {
                $this->messageThread = MessageThread::create($messageThreadPayload);
            } else {
                MessageThread::find($this->messageThread->id)->update([
                    'last_message' => $messageThreadPayload['last_message']
                ]);
            }

            $partcipant1 = ThreadParticipant::firstOrNew(['thread_id' => $this->messageThread->id, 'participant_id' => $messageThreadPayload['sender_id']]);
            if ($partcipant1) {
                $partcipant1->last_read = now();
                $partcipant1->save();
            }

            $partcipant2 = ThreadParticipant::firstOrNew(['thread_id' => $this->messageThread->id, 'participant_id' => $messageThreadPayload['customer_id']]);
            if ($partcipant2) {
                $partcipant2->last_read = null;
                $partcipant2->save();
            }

            return $this->messageThread;
        } catch (Exception $e) {
            throw new Exception($e);
        }
    }

    public function setMessageThread(MessageThread $messageThread): self
    {
        $this->messageThread = $messageThread;
        return $this;
    }

    /**
     * @param int $senderId
     * @param string $message
     * @param string $userType
     * @return mixed
     */
    public function sendText(int $senderId, string $message, string $userType)
    {
        $message = [
            'messageType' => config('constants.messageTypes.text'),
            'thread_id'   => $this->messageThread->id,
            'text'        => $message,
            'sender_id'   => $senderId,
        ];
        if ($userType === config('constants.accountType.business')) {
            $message['sender_business_id'] = $this->messageThread->business_id;
        }

        return Message::create($message);
    }
}
