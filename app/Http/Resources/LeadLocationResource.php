<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LeadLocationResource extends JsonResource
{
    /**
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'locationType' => $this->location_type,
            'business_id'  => $this->business_id,
            'locationId'   => $this->location_id,
            'location'     => LocationResource::make($this->location),
            'radius'       => $this->radius,
            'longitude'    => $this->longitude,
            'latitude'     => $this->latitude,
        ];
    }
}
