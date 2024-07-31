<?php

namespace App\Services\Strategy\Notifications\Business;

use App\Models\User;
use App\Services\Contracts\NotifiableEntity;
use App\Services\Contracts\NotificationStrategy;
use Illuminate\Support\Facades\Mail;

class CancelJobEmailNotification implements NotificationStrategy
{
    public function notify($recipient, $data): void
    {
        $toEmail = $recipient->email;
        $toName = $recipient->user_name;
        $appName = config('config.appName');
        $fromEmail = config('config.noReplyEmail');

        Mail::send('mails.to_customer_job_canceled', $data, static function ($message) use ($toEmail, $toName, $appName, $fromEmail) {
            $message->to($toEmail, $toName)
                ->subject("{$appName} - The business canceled job")
                ->replyTo($fromEmail, config('config.webmaster'));
            $message->from($fromEmail, "{$appName} - The business canceled job");
        });
    }

    public function canNotify(NotifiableEntity $entity, array $settings): bool
    {
        return $settings['notificationChannels']['email'] && ($entity instanceof User);
    }
}
