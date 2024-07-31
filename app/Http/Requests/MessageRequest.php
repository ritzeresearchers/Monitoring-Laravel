<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MessageRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'image'    => 'max:1500',
            'file'     => 'max:1500',
            'threadId' => 'required|exists:message_threads,id',
        ];
    }
}
