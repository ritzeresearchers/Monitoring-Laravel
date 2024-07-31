<?php

namespace App\Services\Strategy\Jobs;

use App\Events\CustomerCanceledJob;
use App\Models\Job;
use App\Services\Contracts\CancellationJobStrategy;

class CustomerCancellationStrategy implements CancellationJobStrategy
{
    public function cancel(Job $job)
    {
        CustomerCanceledJob::dispatch($job);
    }
}
