<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendLeadRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'email'      => 'email',
            'locationId' => 'required|exists:locations,id',
        ];
    }
}
