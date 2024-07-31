<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    /**
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id'             => $this->id,
            'userId'         => $this->user_id,
            'business_id'    => $this->business_id,
            'businessName'   => $this->business->name,
            'jobId'          => $this->job_id,
            'rating'         => $this->rating,
            'content'        => $this->content,
            'poster'         => UserResource::make($this->poster),
            'createdAt'      => $this->created_at,
            'jobPostedAt'    => $this->job ? $this->job->created_at : null,
            'jobCompletedAt' => $this->job ? $this->job->updated_at : null,
        ];
    }
}
