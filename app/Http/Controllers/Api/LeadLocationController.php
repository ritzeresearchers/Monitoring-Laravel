<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeadLocation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Repositories\BusinessRepository;
use App\Repositories\LeadLocationRepository;
use App\Http\Resources\LeadLocationResource;

class LeadLocationController extends Controller
{
    protected BusinessRepository $businessRepository;
    private LeadLocationRepository $leadLocationRepository;

    /**
     * @param BusinessRepository $businessRepository
     * @param LeadLocationRepository $leadLocationRepository
     */
    public function __construct(
        BusinessRepository     $businessRepository,
        LeadLocationRepository $leadLocationRepository)
    {
        $this->businessRepository = $businessRepository;
        $this->leadLocationRepository = $leadLocationRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function postBusinessLocation(Request $request): JsonResponse
    {
        LeadLocation::where('business_id', $this->user()->business_id)->delete();

        $llPayload = [];
        foreach ($request->get('locations') as $location) {
            $payload = [
                'location_type' => $location['locationType'],
                'business_id'   => $this->user()->business_id,
                'radius'        => $location['radius'],
            ];
            if ($location['locationType'] === config('constants.locationTypes.coordinates')) {
                $llPayload[] = array_merge($payload, [
                    'latitude'    => $location['latitude'],
                    'longitude'   => $location['longitude'],
                    'location_id' => null,
                ]);
            } else {
                $llPayload[] = array_merge($payload, [
                    'latitude'    => null,
                    'longitude'   => null,
                    'location_id' => $location['locationId'],
                ]);
            }
        }
        LeadLocation::insert($llPayload);

        if (!in_array($request->get('leadLocationCoverage'), array_keys(config('constants.leadLocationCoverage')))) {
            return $this->respondError('Missing lead location coverage.');
        }

        $this->user()->business->update(['lead_location_coverage' => $request->get('leadLocationCoverage')]);

        $locations = $this->leadLocationRepository->getLeadLocationsByBusinessId($this->user()->business_id);
        $leadLocationCoverage = $this->user()->business->lead_location_coverage;

        return response()->json([
            'leadLocations'        => LeadLocationResource::collection($locations),
            'leadLocationCoverage' => $leadLocationCoverage,
            'isAllOfUk'            => $leadLocationCoverage === 'all',
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function getLocations(): JsonResponse
    {
        $locations = $this->leadLocationRepository->getLeadLocationsByBusinessId($this->user()->business_id);
        $leadLocationCoverage = $this->user()->business->lead_location_coverage;

        return response()->json([
            'leadLocations'        => LeadLocationResource::collection($locations),
            'leadLocationCoverage' => $leadLocationCoverage,
            'isAllOfUk'            => $leadLocationCoverage === 'all',
        ]);
    }
}
