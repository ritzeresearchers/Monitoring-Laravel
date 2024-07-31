<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BusinessNewLeadNotification extends Mailable
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
     * @return BusinessNewLeadNotification
     */
    public function build(): self
    {
        $appName = config('config.appName');
        $fromEmail = config('config.noReplyEmail');

        return $this->markdown('mails.business_new_lead_notification')
            ->subject("{$appName} - New Lead Notification")
            ->from($fromEmail, "{$appName} - New Lead Notification")
            ->replyTo($fromEmail, config('config.webmaster'))
            ->with(['email' => $this->email]);
    }
}
