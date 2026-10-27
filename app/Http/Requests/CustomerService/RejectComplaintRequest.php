<?php

namespace App\Http\Requests\CustomerService;

use Illuminate\Foundation\Http\FormRequest;

class RejectComplaintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'verification_reason' => [
                'required',
                'string',
                'max:1000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'verification_reason.required' =>
                'Please provide a reason for rejecting this complaint.',

            'verification_reason.string' =>
                'The rejection reason must be valid text.',

            'verification_reason.max' =>
                'The rejection reason cannot exceed 1000 characters.',
        ];
    }
}
