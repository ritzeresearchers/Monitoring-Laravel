<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BusinessRegisteredForOwner extends Mailable
{
    use Queueable, SerializesModels;

    protected string $email;
    protected array $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $email, array $data)
    {
        $this->email = $email;
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): BusinessRegisteredForOwner
    {
        $fromEmail = config('config.noReplyEmail');

        return $this->markdown('mails.to_owner_business_registered', $this->data)
            ->subject("Service provider {$this->data['first_name']} {$this->data['last_name']} registered")
            ->from($fromEmail, "Service provider {$this->data['first_name']} {$this->data['last_name']} registered")
            ->replyTo($fromEmail, config('config.webmaster'))
            ->with(['email' => $this->email]);
    }
}
