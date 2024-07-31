<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    /**
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id'                      => $this->id,
            'name'                    => $this->name,
            'is_active'               => $this->is_active,
            'workCategoryId'          => $this->work_category_id,
            'activeBusinessesCount'   => $this->activeBusinessesCount,
            'inactiveBusinessesCount' => $this->inactiveBusinessesCount,
        ];
    }
}
