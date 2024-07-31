<?php

namespace App\Services\Strategy\Notifications\Business;

use App\Services\Contracts\NotifiableEntity;
use App\Services\Contracts\NotificationStrategy;
use App\Services\Contracts\SMSInterface;

class NewLeadSmsNotification implements NotificationStrategy
{
    public function notify($recipient, $data): void
    {
        $smsService = app(SMSInterface::class);

        $smsService::sendText(
            $data['smsRecipient'],
            $data['message']
        );
    }

    public function canNotify(NotifiableEntity $entity, array $settings): bool
    {
        return $settings['notificationChannels']['sms'];
    }
}
