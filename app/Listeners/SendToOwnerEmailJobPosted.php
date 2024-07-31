<?php

namespace App\Listeners;

use App\Events\JobPosted;
use App\Mail\JobPostedForOwner;
use Illuminate\Support\Facades\Mail;

class SendToOwnerEmailJobPosted
{
    /**
     * Handle the event.
     *
     * @param JobPosted $event
     * @return void
     */
    public function handle(JobPosted $event)
    {
        $ownerEmail = config('config.ownerEmail');
        $customerServiceEmail = config('config.customerServiceEmail');

        Mail::to($ownerEmail)->send(new JobPostedForOwner($ownerEmail, $event->data));
        Mail::to($customerServiceEmail)->send(new JobPostedForOwner($customerServiceEmail, $event->data));
    }
}
