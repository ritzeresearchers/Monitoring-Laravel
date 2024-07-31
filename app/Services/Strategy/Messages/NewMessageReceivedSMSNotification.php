<?php

namespace App\Services\Strategy\Messages;

use App\Models\Business;
use App\Models\User;
use App\Services\Contracts\NotifiableEntity;
use App\Services\Contracts\NotificationStrategy;
use App\Services\Contracts\SMSInterface;

class NewMessageReceivedSMSNotification implements NotificationStrategy
{
    public function notify($recipient, $data): void
    {
        $smsService = app(SMSInterface::class);
        $mobileNumber = $recipient->isBusiness() ? $recipient->business->mobile_number : $recipient->mobile_number;
        $isVerified = $recipient->isBusiness() ? $recipient->business->mobile_number_verified_at : $recipient->mobile_number_verified_at;

        if(isset($isVerified) && isValidMobileNumber($mobileNumber)) {
            $smsService::sendText($mobileNumber,
                $data['message']
            );
        }
    }

    public function canNotify(NotifiableEntity $entity, array $settings): bool
    {
        if ($entity instanceof User) {
            return $settings['notificationChannels']['sms'];
        }

        if ($entity instanceof Business) {
            return $settings['notificationChannels']['sms'];
        }    }
}
