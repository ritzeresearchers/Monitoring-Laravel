<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterBusinessRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'title'      => 'required',
            'first_name' => 'required',
            'middle_name' => 'required',
            'last_name'  => 'required',
            'email' => [
                'required',
                'email'
            ],
            'mobile_number'     => 'required',
            'password'          => [
                'required',
                'same:passwordConfirm'
            ],
            'passwordConfirm' => [
                'required',
            ],
            'businessName'      => 'required',
            'locationId'        => [
                'required',
                'exists:locations,id'
            ],
            'workCategories'    => [
                'required',
                'array',
            ],
            'workCategories.*'    => [
                'exists:work_categories,id'
            ],
            'services'    => [
                'required',
                'array',
            ],
            'services.*'    => [
                'exists:services,id'
            ],
            // 'accountHolderName' => 'required',
            // 'accountNumber'     => 'required',
            // 'bankSortCode'      => 'required',
            // 'postcode'          => 'required',
            // 'companyName'       => 'required',
            // 'addressLine1'      => 'required',
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'locationId.exists' => 'Invalid location.',
        ];
    }
}
