<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageThreadResource extends JsonResource
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
            'job'         => JobResource::make($this->job),
            'business'    => BusinessWOReviewResource::make($this->business),
            'customers'   => UserResource::make($this->customer),
            'quote'       => MessageThreadQuoteResource::make($this->quote),
            'lastRead'    => $this->lastRead,
            'lastMessage' => $this->last_message,
            'updatedAt'   => $this->updated_at,
        ];
    }
}
