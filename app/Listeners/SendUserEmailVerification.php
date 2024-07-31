<?php

namespace App\Listeners;

use App\Events\BusinessUserRegistered;
use App\Events\CustomerUserRegistered;
use Exception;
use Illuminate\Support\Facades\Mail;

class SendUserEmailVerification
{
    /**
     * @param CustomerUserRegistered|BusinessUserRegistered $event
     * @return void
     * @throws Exception
     */
    public function handle($event)
    {
        $toEmail = $event->data['email'];
        $toName = $event->data['name'];
        $fromEmail = config('config.noReplyEmail');
        $name = config('config.webmaster');
        $appName = config('config.appName');

        try {
            Mail::send('mails.business_user_verification', [
                'email' => $toEmail,
                'verificationLink' => config('config.appBaseUrl') . '?code=' . ($event->data['verification_code'])
                    . '&user_type=' . $event->data['user_type'],
            ], static function ($message) use ($toEmail, $toName, $appName, $fromEmail, $name) {
                $message->to($toEmail, $toName)
                    ->subject("{$appName} - Email Verification ")
                    ->replyTo($fromEmail, $name);
                $message->from($fromEmail, "{$appName} - Email Verification ");
            });
        } catch (Exception $e) {
            throw new \RuntimeException('Error while sending mail.');
            // throw new \RuntimeException($e);
        }
    }
}
