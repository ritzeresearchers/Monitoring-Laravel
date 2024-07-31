<?php

namespace App\Listeners;

use App\Services\Factory\MessageFactory;
use App\Services\NotificationHandler;
use App\Services\Strategy\Notifications\Business\CancelJobEmailNotification;
use App\Services\Strategy\Notifications\Business\CancelJobInboxNotification;
use App\Services\Strategy\Notifications\Business\CancelJobSmsNotification;
use Illuminate\Support\Facades\DB;

class BusinessCanceledJob
{
    public function handle(\App\Events\BusinessCanceledJob $event): void
    {
        $notificationHandler = new NotificationHandler();

        $recipient = $event->poster;

        $businessEmailStrategy = new CancelJobEmailNotification();
        $businessSmsStrategy = new CancelJobSmsNotification();
        $businessInboxStrategy = new CancelJobInboxNotification();

        $notificationHandler->executeStrategy($businessEmailStrategy, $recipient, ['businessName' => $event->businessName]);
        $notificationHandler->executeStrategy($businessSmsStrategy, $recipient, ['message' => MessageFactory::createMessage(
            MessageFactory::BUSINESS,
            $event->businessName,
            true
        )]);

        DB::beginTransaction();
        $notificationHandler->executeStrategy($businessInboxStrategy, $recipient, [
            'message' => MessageFactory::createMessage(MessageFactory::BUSINESS, $event->businessName, false),
            'job_id' => $event->jobId,
            'senderId' => $event->poster->id
        ]);
        DB::commit();
    }
}
