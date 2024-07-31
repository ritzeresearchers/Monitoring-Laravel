<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use Exception;
use Illuminate\Support\Facades\Mail;

class SendEmailVerification
{
    /**
     * @throws Exception
     */
    public function handle(UserRegistered $event)
    {
        $toEmail = $event->data['email'];
        $toName = $event->data['name'];
        $verificationCode = $event->data['verification_code'];
        $userType = $event->data['user_type'];

        $fromEmail = config('config.noReplyEmail');
        $name = config('config.webmaster');
        $appName = config('config.appName');
        if ($userType === config('constants.accountType.customer'))
            $verificationLink = config('config.appBaseUrl') . "signup?code={$verificationCode}";
        else
            $verificationLink = config('config.appBaseUrl') . "?code={$verificationCode}";
        $template = $userType === config('constants.accountType.customer') ? 'mails.account_verification' : 'mails.account_with_business_verification';

        try {
            Mail::send($template, [
                'email' => $toEmail,
                'verificationLink' => $verificationLink
            ], static function ($message) use ($toEmail, $toName, $appName, $fromEmail, $name) {
                $message->to($toEmail, $toName)
                    ->subject("{$appName} - Email Verification ")
                    ->replyTo($fromEmail, $name);
                $message->from($fromEmail, "{$appName} - Email Verification ");
            });
        } catch (Exception $e) {
            throw new Exception($e);
        }
    }
}
