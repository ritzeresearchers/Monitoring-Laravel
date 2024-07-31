<?php

namespace App\Services\Strategy\Notifications\Customer;

use App\Models\Business;
use App\Services\Contracts\NotifiableEntity;
use App\Services\Contracts\NotificationStrategy;
use Exception;
use Illuminate\Support\Facades\Mail;

class QuoteAcceptedByAnotherBusinessEmailNotification implements NotificationStrategy
{
    public function canNotify(NotifiableEntity $entity, array $settings): bool
    {
        return $settings['notificationChannels']['email'] && ($entity instanceof Business);
    }

    /**
     * @throws Exception
     */
    public function notify($recipient, $data): void
    {
        try {
            $toEmail = $recipient->email;
            $toName = $recipient->name;
            $appName = config('config.appName');
            $fromEmail = config('config.noReplyEmail');

            Mail::send('mails.to_businesses_quote_accepted_by_another', [
                'customerName' => $data['customerName'],
                'jobId'        => $data['jobId']
            ], static function ($message) use ($toEmail, $toName, $appName, $fromEmail) {
                $message->to($toEmail, $toName)
                    ->subject("{$appName} - The customer has chosen another service provider")
                    ->replyTo($fromEmail, config('config.webmaster'));
                $message->from($fromEmail, "{$appName} - The customer has chosen another service provider");
            });
        } catch (Exception $e) {
            throw new Exception($e);
        }
    }
}
