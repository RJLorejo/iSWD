<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignComplaintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'technician_id' => [
                'required',
                'exists:users,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'technician_id.required' =>
                'Please select a technician.',

            'technician_id.exists' =>
                'The selected technician is invalid.',
        ];
    }
}
