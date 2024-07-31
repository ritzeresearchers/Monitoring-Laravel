<?php

namespace App\Services\Strategy\Notifications\Customer;

use App\Models\Business;
use App\Services\Contracts\NotifiableEntity;
use App\Services\Contracts\NotificationStrategy;
use App\Services\Contracts\SMSInterface;

class CancelJobSmsNotification implements NotificationStrategy
{
    public function notify($recipient, $data): void
    {
        $smsService = app(SMSInterface::class);

        $smsService::sendText(
            $recipient->mobile_number,
            $data['message'] . "\n" . config('config.appBaseUrl')
        );
    }

    public function canNotify(NotifiableEntity $entity, array $settings): bool
    {
        return $settings['notificationChannels']['sms'] && ($entity instanceof Business);
    }
}
