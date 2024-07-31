<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageThreadQuoteResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'rate_type'   => $this->rate_type,
            'cost'        => $this->cost,
            'comments'    => $this->comments,
            'createdAt'   => $this->created_at,
            'showDialog'  => '',
            'status'      => $this->status,
        ];
    }
}
