<?php

namespace App\Listeners;

use App\Events\UpdateEmail;
use Exception;
use Illuminate\Support\Facades\Mail;

class SendUpdateEmailVerficationCode
{
    /**
     * @throws Exception
     */
    public function handle(UpdateEmail $event)
    {
        $toEmail = $event->data['email'];
        $toName = $event->data['name'];
        $fromEmail = config('config.noReplyEmail');
        $name = config('config.webmaster');
        $appName = config('config.appName');

        try {
            Mail::send('mails.update_email_verification', [
                'email' => $toEmail,
                'verificationCode' => $event->data['verification_code']
            ], static function ($message) use ($toEmail, $toName, $appName, $fromEmail, $name) {
                $message->to($toEmail, $toName)
                    ->subject("{$appName} - Email Verification ")
                    ->replyTo($fromEmail, $name);
                $message->from($fromEmail, "{$appName} - Email Verification ");
            });
        } catch (Exception $e) {
            throw new Exception('Erro while sending mail.');
        }
    }
}
