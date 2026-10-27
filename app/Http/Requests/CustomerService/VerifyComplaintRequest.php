<?php

namespace App\Http\Requests\CustomerService;

use Illuminate\Foundation\Http\FormRequest;

class VerifyComplaintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'division_id' => [
                'required',
                'integer',
                'exists:divisions,id',
            ],

            'complaint_category_id' => [
                'required',
                'integer',
                'exists:complaint_categories,id',
            ],

            // Optional when verifying a complaint.
            'verification_reason' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'division_id.required' =>
                'Please select the verified division.',

            'division_id.exists' =>
                'The selected division is invalid.',

            'complaint_category_id.required' =>
                'Please select the verified complaint type.',

            'complaint_category_id.exists' =>
                'The selected complaint type is invalid.',
        ];
    }
}
