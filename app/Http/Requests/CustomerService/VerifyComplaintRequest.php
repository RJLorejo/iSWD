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
            'verification_reason' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }
}
