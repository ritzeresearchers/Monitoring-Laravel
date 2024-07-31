<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BusinessWOReviewResource extends JsonResource
{
    /**
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'logo'                   => $this->logo,
            'name'                   => $this->name,
            'description'            => $this->description,
            'location'               => $this->location,
            'address'                => $this->address,
            'mobile_number'          => $this->mobile_number,
            'landline'               => $this->landline,
            'email'                  => $this->email,
            'website'                => $this->website,
            'isVerified'             => $this->is_verified,
            'is_active'              => $this->is_active,
            'is_subscription_active' => $this->is_subscription_active,
            'createdAt'              => $this->created_at,
        ];
    }
}
