<?php

namespace App\Listeners;

use App\Events\NewMessage;
use App\Events\NewMessageSent;
use App\Events\QuoteAccepted;
use App\Http\Resources\MessageResource;
use App\Models\Lead;
use App\Models\Message;
use App\Models\MessageThread;
use App\Models\Quote;
use App\Services\NotificationHandler;
use App\Services\Strategy\Notifications\Customer\QuoteAcceptedByAnotherBusinessEmailNotification;
use App\Services\Strategy\Notifications\Customer\QuoteAcceptedByAnotherBusinessSmsNotification;
use Exception;
use Illuminate\Support\Facades\Log;

class NotifyLeftQuotes
{
    /**
     * @param QuoteAccepted $event
     * @return void
     * @throws Exception
     */
    public function handle(QuoteAccepted $event)
    {
        /* @var $quote Quote */
        foreach ($event->quotes as $quote) {
            $recipient = $quote->business;
            $customerName = $quote->job->poster->user_name;
            $jobId = $quote->job_id;

            $notificationHandler = new NotificationHandler();

            $emailStrategy = new QuoteAcceptedByAnotherBusinessEmailNotification();
            $smsStrategy = new QuoteAcceptedByAnotherBusinessSmsNotification();

            $notificationHandler->executeStrategy($emailStrategy, $recipient, ['jobId' => $jobId, 'customerName' => $customerName]);
            $notificationHandler->executeStrategy($smsStrategy, $recipient, ['jobId' => $jobId, 'customerName' => $customerName]);
        }
    }
}
