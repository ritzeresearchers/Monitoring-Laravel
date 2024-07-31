<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobPostedForOwner extends Mailable
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
    public function build(): JobPostedForOwner
    {
        $fromEmail = config('config.noReplyEmail');

        return $this->markdown('mails.to_owner_job_posted', $this->data)
            ->subject("User {$this->data['first_name']} {$this->data['last_name']} posted a job")
            ->from($fromEmail, "User {$this->data['first_name']} {$this->data['last_name']} posted a job")
            ->replyTo($fromEmail, config('config.webmaster'))
            ->with(['email' => $this->email]);
    }
}
