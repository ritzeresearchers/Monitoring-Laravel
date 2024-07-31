<?php

namespace App\Listeners;

use App\Events\BusinessUserRegistered;
use App\Mail\BusinessRegisteredForOwner;
use Illuminate\Support\Facades\Mail;

class SendToOwnerEmailBusinessRegistered
{
    /**
     * Handle the event.
     *
     * @param BusinessUserRegistered $event
     * @return void
     */
    public function handle(BusinessUserRegistered $event)
    {
        $ownerEmail = config('config.ownerEmail');
        $customerServiceEmail = config('config.customerServiceEmail');

        Mail::to($ownerEmail)->send(new BusinessRegisteredForOwner($ownerEmail, $event->data));
        Mail::to($customerServiceEmail)->send(new BusinessRegisteredForOwner($customerServiceEmail, $event->data));
    }
}
