<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveJobRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'title'           => 'required|max:100',
            'description'     => 'required|max:3000',
            'service_id'      => 'required',
            'category_id'     => 'required',
            'location'        => 'required|exists:locations,location',
            'pages'           => 'required',
            'job_type'        => 'required',
            'rate_type'       => 'required',
            'target_job_done' => 'required',
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'location.exists' => 'Invalid location.',
        ];
    }
}
