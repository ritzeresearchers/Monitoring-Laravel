<?php

namespace App\Repositories;

use App\Models\Lead;

class LeadRepository
{
    use Repository;

    private int $limit = 5;

    protected $model;

    /**
     * @param Lead $lead
     */
    public function __construct(Lead $lead)
    {
        $this->model = $lead;
    }

    /**
     * @param int $businessId
     * @return mixed
     */
    public function findByBusinessId(int $businessId)
    {
        return $this->model::whereHas('job', function ($query) {
            $query->whereIn('status', ['open', 'pending', 'active']);
        })
            ->where('business_id', $businessId)
            ->where('is_not_interested', 0)
            ->where('is_accepted', 0)
            ->where('has_quoted', 0)
            ->where('is_business_hired', 0)
            ->orderBy('id', 'desc')
            ->get();
    }
}
