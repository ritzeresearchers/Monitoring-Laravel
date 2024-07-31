<?php

namespace App\Services\Strategy\Messages;

use App\Mail\MessageNotification as MailMessageNotification;
use App\Models\Business;
use App\Models\User;
use App\Services\Contracts\NotifiableEntity;
use App\Services\Contracts\NotificationStrategy;
use Illuminate\Support\Facades\Mail;

class NewMessageReceivedEmailNotification implements NotificationStrategy
{
    public function notify($recipient, $data): void
    {
        $email = $recipient->isBusiness() ? $recipient->business->email : $recipient->email;
        if(isValidEmail($email)) {
            Mail::to($email)->send(new MailMessageNotification($email));
        }
    }

    public function canNotify(NotifiableEntity $entity, array $settings): bool
    {
        if ($entity instanceof User) {
            return $settings['notificationChannels']['email'];
        }

        if ($entity instanceof Business) {
            return $settings['notificationChannels']['email'];
        }

        return false;
    }
}
