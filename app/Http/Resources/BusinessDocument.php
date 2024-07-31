<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BusinessDocument extends JsonResource
{
    /**
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id'             => $this->id,
            'documentTypeId' => $this->document_type_id,
            'name'           => $this->name,
            'path'           => $this->path,
            'isVerified'     => $this->is_verified,
        ];
    }
}
