<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmUpdateEmailRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'email'            => 'required|email',
            'confirmedEmail'   => 'required|email',
            'verificationCode' => 'required',
        ];
    }
}