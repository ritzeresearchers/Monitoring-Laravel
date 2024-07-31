<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Support\Collection;

/**
 * Trait ProcessRequest
 * @package App\Http\Controllers\Traits
 */
trait ProcessRequest
{
    /**
     * @param $services
     * @return array
     */
    public function processServices($services): array
    {
        return $this
            ->normalize($services)
            ->pluck('id')
            ->toArray();
    }

    /**
     * @param $collection
     * @return Collection
     */
    private function normalize($collection): Collection
    {
        return collect(is_string($collection) ? json_decode($collection, true) : $collection);
    }
}
