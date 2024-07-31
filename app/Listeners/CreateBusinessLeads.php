<?php

namespace App\Listeners;

use App\Events\JobPosted;
use App\Services\LeadsService;

class CreateBusinessLeads
{
    /**
     * @param JobPosted $event
     * @return void
     */
    public function handle(JobPosted $event)
    {
        (new LeadsService())->create($event->job);
    }
}
