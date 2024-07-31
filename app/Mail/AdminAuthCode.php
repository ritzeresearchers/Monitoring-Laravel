<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminAuthCode extends Mailable
{
    use Queueable;
    use SerializesModels;

    protected string $code;

    /**
     * @param string $email
     */
    public function __construct(string $code)
    {
        $this->code = $code;
    }

    /**
     * @return MessageNotification
     */
    public function build(): self
    {
        $appName = config('config.appName');
        $fromEmail = config('config.noReplyEmail');

        return $this->markdown('mails.admin_auth_code')
            ->subject("{$appName} - Auth Code")
            ->from($fromEmail, "{$appName} - Auth Code")
            ->replyTo($fromEmail, config('config.webmaster'))
            ->with(['code' => $this->code]);
    }
}
