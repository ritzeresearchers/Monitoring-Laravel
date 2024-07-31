<?php

namespace App\Listeners;

use App\Events\JobPosted;
use App\Events\UserVerified;

class PostPendingJob
{
    /**
     * Handle the event.
     *
     * @param UserVerified $event
     * @return void
     */
    public function handle(UserVerified $event)
    {
        if ($event->user->jobs->count()) {
            foreach ($event->user->jobs as $job) {
                $job->update(['status' => config('constants.jobStatus.active')]);
                event(new JobPosted([
                    'first_name' => $event->user->first_name,
                    'last_name'  => $event->user->last_name,
                    'serviceId'  => $job->service_id,
                    'jobId'      => $job->id,
                ], $job));
            }
        }
    }
}
