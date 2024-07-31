<?php

namespace App\Services;

use App\Models\Location;
use App\Models\LeadLocation;
use App\Models\Business;
use Haversini\Haversini;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class BusinessService
{
    public const LIMIT = 20;

    /**
     * @param $request
     * @return array|Collection
     */
    public static function getAll($request)
    {
        $sortBy = $request->input('sortBy');
        $businessQuery = Business::query();
        if ($sortBy === 'rating') {
            $businessQuery
                ->withAvg('reviews', 'rating')
                ->orderBy('reviews_avg_rating', 'desc');
        }
        if ($sortBy === 'review') {
            $businessQuery
                ->withCount('reviews')
                ->orderBy('reviews_count', 'desc');
        }
        if ($sortBy === 'name') {
            $businessQuery
                ->with('reviews')
                ->orderBy('name');
        }
        if ($sortBy === 'nearest-location') {
            return self::getNeareastBusinessByLocation(Location::find($request->locationId));
        }

        if ($categoryId = $request->categoryId) {
            $businessQuery->with(['categories' => static function ($query) use ($categoryId) {
                $query->where('work_category_id', $categoryId);
            }]);
        }

        return $businessQuery
            ->limit(self::LIMIT)
            ->offset(($request->input('page', 1) - 1) * self::LIMIT)
            ->get();
    }

    /**
     * @param $location
     * @return array
     */
    public static function getNeareastBusinessByLocation($location): array
    {
        $latitude = $location->latitude;
        $longitude = $location->longitude;

        $locationsByCoordinates = LeadLocation::with('location')
            ->where('location_type', config('constants.locationTypes.coordinates'))
            ->get()
            ->toArray();

        $filteredLocation1 = array_filter($locationsByCoordinates, static function ($item) use ($latitude, $longitude) {
            if ($item['latitude'] && $item['longitude'] && $item['radius']) {
                $distance = Haversini::calculate(
                    $item['latitude'],
                    $item['longitude'],
                    $latitude,
                    $longitude,
                    'm'
                );
                return $distance < $item['radius'];
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

                $item['distance'] = $distance;

                return ($distance < $item['radius']);
            }
        });

        $businessIds = array_column(array_merge($filteredLocation1, $filteredLocation2), 'business_id');

        $bs = Business::with('leadCoverage')
            ->whereIn('id', $businessIds)
            ->get()
            ->toArray();

        return collect($bs)->map(static function ($business) use ($latitude, $longitude) {
            $leadLocations = $business['lead_coverage'];

            $distances = collect($leadLocations)->map(static function ($ll) use ($latitude, $longitude) {
                $distance = 0;
                if ($ll['location_type'] == config('constants.locationTypes.address')) {
                    $location = Location::where('id', $ll['location_id'])->first();
                    if ($location) {
                        $distance = Haversini::calculate(
                            $location['latitude'],
                            $location['longitude'],
                            $latitude,
                            $longitude,
                            'm'
                        );
                    }

                } elseif ($ll['latitude'] !== null && $ll['longitude'] !== null) {
                    $distance = Haversini::calculate(
                        $ll['latitude'],
                        $ll['longitude'],
                        $latitude,
                        $longitude,
                        'm'
                    );
                }

                return $distance;
            })->filter(static function ($d) {
                return $d > 0;
            })->min();

            $business['distance'] = $distances;

            return $business;
        })
            ->sortBy('distance')
            ->values()
            ->all();
    }

    /**
     * @param $location
     * @param $request
     * @return Builder[]|Collection
     */
    public static function getNearestBusiness($location, $request)
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

        $businessIds = array_column(array_merge($filteredLocation1, $filteredLocation2), 'business_id');
        $businessQuery = Business::query()
            ->active()
            ->whereIn('id', $businessIds);

        $sortBy = $request->input('sortBy');
        if ($sortBy === 'rating') {
            $businessQuery
                ->withAvg('reviews', 'rating')
                ->orderBy('reviews_avg_rating', 'desc');
        }
        if ($sortBy === 'review') {
            $businessQuery
                ->withCount('reviews')
                ->orderBy('reviews_count', 'desc');
        }
        if ($sortBy === 'name') {
            $businessQuery
                ->with('reviews')
                ->orderBy('name');
        }

        $categoryId = $request->category_id;
        $businessQuery->whereHas('categories', static function ($query) use ($categoryId) {
            return $query->where('work_category_id', $categoryId);
        });

        return $businessQuery
            ->limit(self::LIMIT)
            ->offset(($request->input('page', 1) - 1) * self::LIMIT)->get();
    }
}
