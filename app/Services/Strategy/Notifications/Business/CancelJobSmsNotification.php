<?php

namespace App\Services\Strategy\Notifications\Business;

use App\Models\User;
use App\Services\Contracts\NotifiableEntity;
use App\Services\Contracts\NotificationStrategy;
use App\Services\Contracts\SMSInterface;
use Illuminate\Support\Facades\Log;

class CancelJobSmsNotification implements NotificationStrategy
{
    public function notify($recipient, $data): void
    {
        try {
            $smsService = app(SMSInterface::class);

            $smsService::sendText(
                $recipient->mobile_number,
                $data['message']
            );
        } catch (\Throwable $throwable) {
            Log::debug($throwable->getMessage());
        }

    }

    public function canNotify(NotifiableEntity $entity, array $settings): bool
    {
        return $settings['notificationChannels']['sms'] && ($entity instanceof User);
    }
}
