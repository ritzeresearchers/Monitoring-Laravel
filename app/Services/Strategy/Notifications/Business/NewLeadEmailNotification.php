<?php

namespace App\Services\Strategy\Notifications\Business;

use App\Mail\BusinessNewLeadNotification;
use App\Services\Contracts\NotifiableEntity;
use App\Services\Contracts\NotificationStrategy;
use Illuminate\Support\Facades\Mail;

class NewLeadEmailNotification implements NotificationStrategy
{
    public function notify($recipient, $data): void
    {
        Mail::to($data['emailRecipient'])->send(new BusinessNewLeadNotification($data['emailRecipient']));
    }

    public function canNotify(NotifiableEntity $entity, array $settings): bool
    {
        return $settings['notificationChannels']['email'];
    }
}
