<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BargainResource extends JsonResource
{
    /**
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id'        => $this->id,
            'rate_type' => $this->rate_type,
            'cost'      => $this->cost,
            'status'    => $this->status,
        ];
    }
}
