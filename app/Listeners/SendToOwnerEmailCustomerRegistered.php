<?php

namespace App\Listeners;

use App\Events\CustomerUserRegistered;
use App\Mail\CustomerRegisteredForOwner;
use Illuminate\Support\Facades\Mail;

class SendToOwnerEmailCustomerRegistered
{
    /**
     * Handle the event.
     *
     * @param CustomerUserRegistered $event
     * @return void
     */
    public function handle(CustomerUserRegistered $event)
    {
        $ownerEmail = config('config.ownerEmail');
        $customerServiceEmail = config('config.customerServiceEmail');

        Mail::to($ownerEmail)->send(new CustomerRegisteredForOwner($ownerEmail, $event->data));
        Mail::to($customerServiceEmail)->send(new CustomerRegisteredForOwner($customerServiceEmail, $event->data));
    }
}
