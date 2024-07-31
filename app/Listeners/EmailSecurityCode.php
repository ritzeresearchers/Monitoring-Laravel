<?php

namespace App\Listeners;

use App\Events\RequestSecurityCode;
use Exception;
use Illuminate\Support\Facades\Mail;

class EmailSecurityCode
{
    /**
     * @throws Exception
     */
    public function handle(RequestSecurityCode $event)
    {
        $toEmail = $event->data['email'];
        $securityCode = $event->data['security_code'];
        $toName = $event->data['name'];
        $fromEmail = config('config.noReplyEmail');
        $name = config('config.webmaster');
        $appName = config('config.appName');
        try {
            Mail::send('mails.security_code', [
                'securityCode' => $securityCode,
                'link' => config('config.changePasswordUrl') . '?securityCode=' . $securityCode,
            ], static function ($message) use ($toEmail, $toName, $appName, $fromEmail, $name) {
                $message->to($toEmail, $toName)
                    ->subject("{$appName} - Security Code")
                    ->replyTo($fromEmail, $name);
                $message->from($fromEmail, "{$appName} - Security Code");
            });
        } catch (Exception $e) {
            print('Caught exception: ' . $e->getMessage());
            throw new Exception('Erro while sending mail.');
        }
    }
}
