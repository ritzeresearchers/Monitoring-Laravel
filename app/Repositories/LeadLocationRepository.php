<?php

namespace App\Repositories;

use App\Models\LeadLocation;

class LeadLocationRepository
{
    use Repository;

    protected $model;

    /**
     * @param LeadLocation $leadLocation
     */
    public function __construct(LeadLocation $leadLocation)
    {
        $this->model = $leadLocation;
    }

    /**
     * @param int $businessId
     * @return array
     */
    public function getLeadLocationsByBusinessId(int $businessId): array
    {
        $locationsByCoordinates = $this->model::where('business_id', $businessId)
            ->where('location_type', config('constants.locationTypes.coordinates'))
            ->get();

        $locations = $this->model::where('business_id', $businessId)
            ->where('location_type', config('constants.locationTypes.address'))
            ->with('location')
            ->get();

        return [...$locationsByCoordinates, ...$locations];
    }
}
