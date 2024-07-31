<?php

namespace App\Listeners;

use App\Services\Factory\MessageFactory;
use App\Services\NotificationHandler;
use App\Services\Strategy\Notifications\Customer\CancelJobInboxNotification;
use App\Services\Strategy\Notifications\Customer\CancelJobSmsNotification;
use App\Services\Strategy\Notifications\Customer\CancelJobEmailNotification;
use Exception;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

class CustomerCanceledJob
{
    /**
     * @param \App\Events\CustomerCanceledJob $event
     * @return void
     * @throws Exception
     */
    public function handle(\App\Events\CustomerCanceledJob $event): void
    {
        try {
            $notificationHandler = new NotificationHandler();

            $recipient = $event->job->hiredBusiness;

            $customerEmailStrategy = new CancelJobEmailNotification();
            $customerSmsStrategy = new CancelJobSmsNotification();
            $inboxSmsStrategy = new CancelJobInboxNotification();

            $notificationHandler->executeStrategy($customerEmailStrategy, $recipient, ['customerName' => $event->job->poster->user_name]);
            $notificationHandler->executeStrategy($customerSmsStrategy, $recipient, ['message' => MessageFactory::createMessage(
                MessageFactory::CUSTOMER,
                $event->job->poster->name,
                true
            )]);

            DB::beginTransaction();
            $notificationHandler->executeStrategy($inboxSmsStrategy, $recipient, [
                'message' => MessageFactory::createMessage(MessageFactory::CUSTOMER, $event->job->poster->name, false),
                'job_id' => $event->job->id,
                'senderId' => $event->job->poster->id
            ]);
            DB::commit();
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw new RuntimeException();
        }

    }
}
