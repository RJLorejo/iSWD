<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDepartmentRequest extends FormRequest
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
                Rule::unique('departments')
                    ->ignore($this->department),
            ],

            'description' => 'nullable|string',

            'is_active' => 'nullable|boolean',

        ];
    }
}
