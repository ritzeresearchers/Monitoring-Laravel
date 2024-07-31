<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WorkCategoryResource extends JsonResource
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
            'activeBusinesses'        => $this->getActiveBusinessesCountAttribute(),
            'businesses'              => BusinessResource::collection($this->businesses),
            'activeBusinessesCount'   => $this->activeBusinessesCount,
            'activeBusinessesNames'   => $this->activeBusinessesNames,
            'inactiveBusinessesCount' => $this->inactiveBusinessesCount,
            'inactiveBusinessesNames' => $this->inactiveBusinessesNames
        ];
    }
}
