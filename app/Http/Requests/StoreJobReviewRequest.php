<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobReviewRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'rating'      => 'required',
            'business_id' => 'required|exists:businesses,id',
        ];
    }
}
