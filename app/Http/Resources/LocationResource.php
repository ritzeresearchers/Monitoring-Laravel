<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
{
    /**
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id'        => $this->id,
            'location'  => $this->location,
            'town'      => $this->town,
            'country'   => $this->country,
            'region'    => $this->region,
            'latitude'  => $this->latitude,
            'longitude' => $this->longitude,
            'postcode'  => $this->postcode,
        ];
    }
}
