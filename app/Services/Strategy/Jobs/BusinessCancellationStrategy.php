<?php

namespace App\Services\Strategy\Jobs;

use App\Events\BusinessCanceledJob;
use App\Models\Job;
use App\Services\Contracts\CancellationJobStrategy;

class BusinessCancellationStrategy implements CancellationJobStrategy
{
    public function cancel(Job $job)
    {
        $businessName = $job->hiredBusiness->name;
        $poster = $job->poster;

        BusinessCanceledJob::dispatch($job->id, $businessName, $poster);
    }
}
