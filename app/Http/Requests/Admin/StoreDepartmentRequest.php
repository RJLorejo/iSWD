<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'department_name' => [
                'required',
                'string',
                'max:255',
                'unique:departments,department_name'
            ],

            'description' => 'nullable|string',

            'is_active' => 'nullable|boolean',

        ];
    }
}
