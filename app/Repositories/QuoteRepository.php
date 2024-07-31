<?php

namespace App\Repositories;

use App\Models\Quote;

class QuoteRepository
{
    use Repository;

    protected $model;

    /**
     * @param Quote $quote
     */
    public function __construct(Quote $quote)
    {
        $this->model = $quote;
    }

    /**
     * @param int $userId
     * @return mixed
     */
    public function findQuotedJobsByPosterId(int $userId)
    {
        return $this->model::whereHas('job', function ($query) use ($userId) {
            return $query
                ->where('poster_id', $userId)
                ->where('hired_business_id', null);
        })
            ->where('is_cancelled', 0)
            ->with('job')
            ->groupBy('job_id')
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * @param int $businessId
     * @return mixed
     */
    public function findQuotedLeadsByBusinessId(int $businessId)
    {
        return $this->model::where('business_id', $businessId)
            ->whereHas('job', function ($query) {
                return $query->where('hired_business_id', null);
            })
            ->where('is_accepted', 0)
            ->where('is_cancelled', 0)
            ->orderBy('id', 'desc')
            ->get();
    }
}
