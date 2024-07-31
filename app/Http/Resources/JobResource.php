<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use function config;

class JobResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $quote = $this->quotes()->where('is_accepted', 1)->first();

        return [
            'id'                       => $this->id,
            'jobNumber'                => $this->job_number,
            'title'                    => $this->title,
            'pages'                   =>$this->pages,
            'paymentstatus'           =>$this->paymentstatus,
            'amount'                   => $this->amount,
            'description'              => nl2br($this->description, false),
            'posterId'                 => $this->poster_id,
            'user'                     => UserResource::make($this->poster),
            'quote'                    => $quote !== null ? QuoteOnlyResource::make($quote) : null,
            'cost'                     => $quote !== null ? $quote->cost : null,
            'rate_type'                => $quote !== null ? $quote->rate_type : null,
            'business'                 => BusinessResource::make($this->hiredBusiness),
            'category_id'              => $this->category_id,
            'service_id'               => $this->service_id,
            'service'                  => $this->service,
            'is_active'                => $this->is_active,
            'location'                 => $this->location->location ?? '',
            'quotesCount'              => $this->quotes_count ?? 0,
            'other_details'            => $this->other_details,
            'job_type'                 => $this->job_type,
            'target_job_done'          => $this->target_job_done,
            'targetCompletionDatetime' => $this->target_completion_datetime,
            $this->mergeWhen($this->status === config('constants.jobStatus.finished'), [
                'reviews'              => $this->reviews()->count() ? ReviewResource::collection($this->reviews) : null,
                'finished_by'          => $this->hiredBusiness ? UserResource::make($this->hiredBusiness->user) : null,
            ]),
            'status'                   => $this->status,
            'createdAt'                => $this->created_at,
        ];
    }
}
