<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterCustomerRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'title'      => 'required',
            'first_name' => 'required',
            'middle_name' => 'required',
            'last_name'  => 'required',
            // 'name'  =>  [
            //     'required_without_all:firstName,lastName',
            //     'regex:/^[a-z]+\s+[a-z]+$/i',
            // ],
            'email' =>  'required|email|unique:users,email',
            'mobile_number' => 'required|unique:users,mobile_number',
            'password'          => [
                'required',
                'same:passwordConfirm',
            ],
            'passwordConfirm' => [
                'required',
            ],
        ];
    }
}
