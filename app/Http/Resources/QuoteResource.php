<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuoteResource extends JsonResource
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
            'user'        => UserResource::make($this->job->poster),
            'business_id' => $this->business_id,
            'rate_type'   => $this->rate_type,
            'cost'        => $this->cost,
            'comments'    => $this->comments,
            'business'    => BusinessResource::make($this->business),
            'job'         => JobResource::make($this->job),
            'createdAt'   => $this->created_at,
            'showDialog'  => '',
            'status'      => $this->status,
        ];
    }
}
