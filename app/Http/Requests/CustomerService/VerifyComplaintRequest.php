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

            'verification_reason' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ];
    }
}
