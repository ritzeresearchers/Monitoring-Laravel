<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BusinessResource extends JsonResource
{
    /**
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id'                     => $this->id,
            'userName'               => trim($this->user->name . ' ' . $this->user->first_name . ' ' . $this->user->last_name),
            'userId'                 => $this->user_id,
            'logo'                   => $this->logo,
            'name'                   => $this->name,
            'description'            => $this->description,
            'location'               => $this->location,
            'address'                => $this->address,
            'mobile_number'          => $this->mobile_number,
            'landline'               => $this->landline,
            'email'                  => $this->email,
            'userEmail'              => $this->user ? $this->user->email : '',
            'website'                => $this->website,
            'mobileNumberVerifiedAt' => $this->mobile_number_verified_at,
            'emailVerifiedAt'        => $this->email_verified_at,
            'is_active'              => $this->is_active,
            'code'                   => $this->user ? $this->user->verification_code : '',
            'isVerified'             => $this->user && $this->user->email_verified_at,
            'createdAt'              => $this->user ? $this->user->created_at : '',
            'deletedAt'              => $this->deleted_at,
            'reviewsCount'           => $this->reviews->count(),
            'workCategories'         => $this->categories->map(function ($cat) {return $cat->id;}),
            'workCategoriesName'     => $this->categories->pluck('name')->implode(','),
            'quotedJobs'             => $this->quotes()->count(),
            'finishedJobsCount'      => $this->jobs()->finished()->count(),
            'canceledJobsCount'      => $this->jobs()->canceled()->count(),
            'reviews_avg_rating'     => $this->reviews_avg_rating,
            'is_subscription_active' => $this->is_subscription_active,
            'bankDetails'            => BankDetailResource::make($this->bankDetail),
            'documents'              => BusinessDocument::collection($this->documents),
            'reviews'                => ReviewResource::collection($this->reviews),
            'services'               => ServiceResource::collection($this->services),
        ];
    }
}
