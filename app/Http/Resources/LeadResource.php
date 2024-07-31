<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LeadResource extends JsonResource
{
    /**
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'jobId'       => $this->job_id,
            'user'        => $this->job ? UserResource::make($this->job->poster) : null,
            'business_id' => $this->business_id,
            'business'    => BusinessResource::make($this->business),
            'job'         => JobResource::make($this->job),
            'rate_type'   => 'hourly',
            'moreInfoMsg' => '',
            'createdAt'   => $this->created_at,
        ];
    }
}
