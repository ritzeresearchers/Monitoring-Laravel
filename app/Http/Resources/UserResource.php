<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'email'           => $this->email,
            'first_name'      => $this->first_name,
            'last_name'       => $this->last_name,
            'mobile_number'   => $this->mobile_number,
            'userType'        => $this->user_type,
            'is_active'       => $this->is_active,
            'avatar'          => $this->avatar,
            'emailVerifiedAt' => $this->email_verified_at,
            'createdAt'       => $this->created_at,
            'deletedAt'       => $this->deleted_at,
            // 'subscription' => $this->subscribed('account_subscription'),
            'subscription'  => true,
            'isbusiness' => $this->isBusiness()
        ];
    }
}
