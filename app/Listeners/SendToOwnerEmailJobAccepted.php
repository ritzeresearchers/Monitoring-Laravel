<?php

namespace App\Listeners;

use App\Events\BusinessWasAcceptedForJob;
use App\Mail\JobAcceptedForOwner;
use Illuminate\Support\Facades\Mail;

class SendToOwnerEmailJobAccepted
{
    /**
     * Handle the event.
     *
     * @param BusinessWasAcceptedForJob $event
     * @return void
     */
    public function handle(BusinessWasAcceptedForJob $event)
    {
        $ownerEmail = config('config.ownerEmail');
        $customerServiceEmail = config('config.customerServiceEmail');

        Mail::to($ownerEmail)->send(new JobAcceptedForOwner($ownerEmail, $event->data));
        Mail::to($customerServiceEmail)->send(new JobAcceptedForOwner($customerServiceEmail, $event->data));
    }
}
