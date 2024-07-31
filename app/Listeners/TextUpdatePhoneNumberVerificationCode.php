<?php

namespace App\Listeners;

use App\Events\UpdatePhoneNumber;
use App\Services\Contracts\SMSInterface;

class TextUpdatePhoneNumberVerificationCode
{
    /**
     * @param UpdatePhoneNumber $event
     * @return void
     */
    public function handle(UpdatePhoneNumber $event)
    {
        $appName = config('config.appName');

        $smsService = app(SMSInterface::class);

        $smsService::sendText($event->data['mobile_number'], $event->data['mobile_number_verification_code'] . " is your {$appName} verification code.");
    }
}
