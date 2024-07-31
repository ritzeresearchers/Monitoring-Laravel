<?php

namespace App\Services;

use App\Models\Job;
use App\Models\Lead;
use App\Models\LeadLocation;
use App\Models\Business;
use App\Events\JobLeadPosted;
use Carbon\Carbon;
use Haversini\Haversini;

class LeadsService
{
    /**
     * @param Job $job
     * @return void
     */
    public function create(Job $job)
    {
        $leadLocationsBusinessId = $this->getCoverageLocationsByCoordinates($job->location);
        $businessWithAllRegionCoverageIds = $this->getAllLeadLocationCoverageAllIds();
        $intersectedBusinessIds = array_unique(array_merge($leadLocationsBusinessId, $businessWithAllRegionCoverageIds), SORT_REGULAR);

        $businessIds = Business::whereIn('id', $intersectedBusinessIds)
            ->whereHas('categories', function($query) use ($job) { $query->where('work_category_id', $job->category_id);})
            ->pluck('id')
            ->toArray();

        $leads = [];
        foreach ($businessIds as $bId) {
            $leads[] = [
                'job_id'            => $job->id,
                'business_id'       => $bId,
                'is_not_interested' => 0,
                'is_accepted'       => 0,
                'created_at'        => Carbon::now()
            ];

            broadcast(new JobLeadPosted(0, $bId, []))->toOthers();
        }

        return Lead::insert($leads);
    }

    /**
     * @return mixed
     */
    public function getAllLeadLocationCoverageAllIds()
    {
        return Business::where('lead_location_coverage', 'all')
            ->get(['id'])
            ->pluck(['id'])
            ->toArray();
    }

    /**
     * @param $location
     * @return array
     */
    public function getCoverageLocationsByCoordinates($location): array
    {
        $latitude = $location->latitude;
        $longitude = $location->longitude;

        $locationsByCoordinates = LeadLocation::where('location_type', config('constants.locationTypes.coordinates'))->get()->toArray();

        $filteredLocation1 = array_filter($locationsByCoordinates, static function ($item) use ($latitude, $longitude) {
            if ($item['latitude'] && $item['longitude'] && $item['radius']) {
                $distance = Haversini::calculate(
                    $item['latitude'],
                    $item['longitude'],
                    $latitude,
                    $longitude,
                    'm'
                );
                return ($distance < $item['radius']);
            }
        });

        $locationsByAddress = LeadLocation::where('location_type', config('constants.locationTypes.address'))
            ->with('location')
            ->get()
            ->toArray();

        $filteredLocation2 = array_filter($locationsByAddress, static function ($item) use ($latitude, $longitude) {
            if ($item['location']['latitude'] && $item['location']['longitude'] && $item['radius']) {
                $distance = Haversini::calculate(
                    $item['location']['latitude'],
                    $item['location']['longitude'],
                    $latitude,
                    $longitude,
                    'm'
                );

                return ($distance < $item['radius']);
            }
        });

        $mergedLocations = array_merge($filteredLocation1, $filteredLocation2);

        return array_column($mergedLocations, 'business_id');
    }
}
