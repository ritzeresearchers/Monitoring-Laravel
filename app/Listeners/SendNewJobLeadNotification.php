<?php

namespace App\Listeners;

use App\Events\JobLeadPosted;
use App\Models\Business;
use App\Services\NotificationHandler;
use App\Services\Strategy\Notifications\Business\NewLeadEmailNotification;
use App\Services\Strategy\Notifications\Business\NewLeadSmsNotification;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;

class SendNewJobLeadNotification
{
    /**
     * Handle the event.
     *
     * @param JobLeadPosted $event
     * @return void
     * @throws ConfigurationException
     * @throws TwilioException
     */
    public function handle(JobLeadPosted $event): void
    {
        $business = Business::find($event->businessId);
        $appName = config('config.appName');

        $notificationHandler = new NotificationHandler();

        $recipient = $business->user;
        $emailRecipient = isValidEmail($business->email);
        $smsRecipient = isValidMobileNumber($business->mobile_number);

        if ($emailRecipient) {
            $businessEmailStrategy = new NewLeadEmailNotification();
            $notificationHandler->executeStrategy($businessEmailStrategy, $recipient, ['emailRecipient' => $emailRecipient]);
        }

        if ($smsRecipient) {
            $message = sprintf('You have got new lead in your %s account. Please login to view new leads. %s%s',
                $appName,
                "\n",
                config('config.appBaseUrl')
            );
            $businessSmsStrategy = new NewLeadSmsNotification();
            $notificationHandler->executeStrategy($businessSmsStrategy, $recipient, ['message' => $message, 'smsRecipient' => $smsRecipient]);
        }
    }
}
