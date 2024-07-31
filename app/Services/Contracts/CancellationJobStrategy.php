<?php

namespace App\Services\Contracts;

use App\Models\Job;

interface CancellationJobStrategy
{
    public function cancel(Job $job);
}
