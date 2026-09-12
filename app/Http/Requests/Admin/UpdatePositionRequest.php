<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePositionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'position_name' => [

                'required',

                Rule::unique('positions')
                    ->ignore($this->position),

            ],

            'department_id' => 'required|exists:departments,id',

            'description' => 'nullable|string',

            'is_active' => 'nullable|boolean',

        ];
    }
}
