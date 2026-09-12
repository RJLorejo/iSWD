<?php

namespace App\Http\Requests\Manager;

use Illuminate\Foundation\Http\FormRequest;

class AssignComplaintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check()
            && auth()->user()->hasRole('Maintenance Manager');
    }

    public function rules(): array
    {
        return [
            'technician_ids' => [
                'required',
                'array',
                'min:1',
                'max:3',
            ],

            'technician_ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:users,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'technician_ids.required' =>
                'Please select at least one Maintenance Technician.',

            'technician_ids.array' =>
                'Invalid technician selection.',

            'technician_ids.min' =>
                'Please select at least one Maintenance Technician.',

            'technician_ids.max' =>
                'You can assign a maximum of three technicians to one complaint.',

            'technician_ids.*.exists' =>
                'One or more selected technicians are invalid.',

            'technician_ids.*.distinct' =>
                'A technician cannot be assigned more than once.',
        ];
    }
}
