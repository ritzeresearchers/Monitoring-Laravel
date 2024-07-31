<?php

namespace App\Listeners;

use App\Events\NewMessageSent;
use App\Models\User;
use App\Services\NotificationHandler;
use App\Services\Strategy\Messages\NewMessageReceivedEmailNotification;
use App\Services\Strategy\Messages\NewMessageReceivedSMSNotification;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;

class SendExternalNotification
{
    protected static string $messageText = 'Someone sent you a message in your account. Please login to view';

    /**
     * @param NewMessageSent $event
     * @return void
     * @throws ConfigurationException
     * @throws TwilioException
     */
    public function handle(NewMessageSent $event): void
    {
        $otherUser = User::find($event->otherParticipantId);

        $notificationHandler = new NotificationHandler();

        $emailStrategy = new NewMessageReceivedEmailNotification();
        $notificationHandler->executeStrategy($emailStrategy, $otherUser, []);

        $smsStrategy = new NewMessageReceivedSMSNotification();

        $message = sprintf('%s %s%s', self::$messageText, "\n", config('config.appBaseUrl'));
        $notificationHandler->executeStrategy($smsStrategy, $otherUser, ['message' => $message]);
    }
}
