<?php

namespace App\Http\Requests\Required;

use Illuminate\Foundation\Http\FormRequest;

class RequiredEmailRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
        ];
    }
}
