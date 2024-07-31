<?php

namespace App\Listeners;

use App\Events\GuestMessageSubmitted;
use Exception;
use Illuminate\Support\Facades\Mail;

class EmailGuestMessageToAdmin
{
    /**
     * @throws Exception
     */
    public function handle(GuestMessageSubmitted $event)
    {
        $name = $event->data['name'];
        $fromEmail = $event->data['email'];
        $toEmail = config('config.contactUsRecipient');
        $toName = config('config.webmaster');

        try {
            Mail::send('mails.inquiry', [
                'name' => $name,
                'bodyMessage' => $event->data['bodyMessage']
            ], static function ($message) use ($toEmail, $toName, $fromEmail, $name) {
                $message->to($toEmail, $toName)
                    ->subject('Message inquiry')
                    ->replyTo($fromEmail, $name);
                $message->from($fromEmail, 'Message inquiry');
            });
        } catch (Exception $e) {
            print('Caught exception: ' . $e->getMessage());
            throw new Exception('Erro while sending mail.');
        }
    }
}
