<?php

namespace App\Services\Strategy\Notifications\Customer;

use App\Models\Business;
use App\Services\Contracts\NotifiableEntity;
use App\Services\Contracts\NotificationStrategy;
use App\Services\Contracts\SMSInterface;

class QuoteAcceptedByAnotherBusinessSmsNotification implements NotificationStrategy
{
    public function canNotify(NotifiableEntity $entity, array $settings): bool
    {
        return $settings['notificationChannels']['email'] && ($entity instanceof Business);
    }

    public function notify($recipient, $data): void
    {
        $smsService = app(SMSInterface::class);

        $smsService::sendText(
            $recipient->mobile_number,
            "Thank you for your interest! The customer " .
            "{$data['customerName']} has chosen another service provider for the job with " .
            "ID {$data['jobId']}. Please keep an eye out for future opportunities."
        );
    }
}
