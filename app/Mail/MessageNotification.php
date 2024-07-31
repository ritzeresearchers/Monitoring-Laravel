<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MessageNotification extends Mailable
{
    use Queueable;
    use SerializesModels;

    protected string $email;

    /**
     * @param string $email
     */
    public function __construct(string $email)
    {
        $this->email = $email;
    }

    /**
     * @return MessageNotification
     */
    public function build(): self
    {
        $appName = config('config.appName');
        $fromEmail = config('config.noReplyEmail');

        return $this->markdown('mails.message_notification')
            ->subject("{$appName} - Message Notification")
            ->from($fromEmail, "{$appName} - Message Notification")
            ->replyTo($fromEmail, config('config.webmaster'))
            ->with(['email' => $this->email]);
    }
}
