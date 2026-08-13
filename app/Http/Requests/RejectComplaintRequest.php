<?php

namespace App\Http\Requests;

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
        ];
    }
}
