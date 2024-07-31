<?php

namespace App\Http\Requests;

use App\Http\Requests\Required\RequiredEmailRequest;

class SendInquiryRequest extends RequiredEmailRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'name'        => 'required',
            'bodyMessage' => 'required',
        ]);
    }
}
