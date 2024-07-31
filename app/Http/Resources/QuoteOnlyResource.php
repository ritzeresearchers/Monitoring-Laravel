<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuoteOnlyResource extends JsonResource
{
    /**
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'jobId'        => $this->job_id,
            'business_id'  => $this->business_id,
            'rate_type'    => $this->rate_type,
            'cost'         => $this->cost,
            'createdAt'    => $this->created_at,
            'updatedAt'    => $this->updated_at,
            'showDialog'   => '',
            'status'       => $this->status,
        ];
    }
}
