<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePositionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'position_name' => 'required|max:255|unique:positions',

            'department_id' => 'required|exists:departments,id',

            'description' => 'nullable|string',

        ];
    }
}
