<?php

namespace App\Services\Strategy\Notifications\Customer;

use App\Models\Business;
use App\Services\Contracts\NotifiableEntity;
use App\Services\Contracts\NotificationStrategy;
use Illuminate\Support\Facades\Mail;

class CancelJobEmailNotification implements NotificationStrategy
{
    public function notify($recipient, $data): void
    {
        $toEmail = $recipient->email;
        $toName = $recipient->name;
        $appName = config('config.appName');
        $fromEmail = config('config.noReplyEmail');

        Mail::send('mails.to_businesses_job_canceled', $data, static function ($message) use ($toEmail, $toName, $appName, $fromEmail) {
            $message->to($toEmail, $toName)
                ->subject("{$appName} - The customer canceled job")
                ->replyTo($fromEmail, config('config.webmaster'));
            $message->from($fromEmail, "{$appName} - The customer canceled job");
        });
    }

    public function canNotify(NotifiableEntity $entity, array $settings): bool
    {
        return $settings['notificationChannels']['email'] && ($entity instanceof Business);
    }
}
