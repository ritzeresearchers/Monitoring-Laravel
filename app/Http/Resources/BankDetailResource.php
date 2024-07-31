<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BankDetailResource extends JsonResource
{
    /**
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'account_holder_name' => $this->account_holder_name,
            'account_number'      => $this->account_number,
            'bank_sort_code'      => $this->bank_sort_code,
            'line_1'              => $this->line1,
            'line_2'              => $this->line2,
            'post_code'           => $this->post_code
        ];
    }
}
