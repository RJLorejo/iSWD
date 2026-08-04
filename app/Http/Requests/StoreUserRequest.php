<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'employee_id' => 'required|unique:users',

            'first_name' => 'required|string|max:100',

            'middle_name' => 'nullable|string|max:100',

            'last_name' => 'required|string|max:100',

            'suffix' => 'nullable|string|max:20',

            'email' => 'required|email|unique:users',

            'phone' => 'nullable|string|max:20',

            'department_id' => 'required|exists:departments,id',

            'position_id' => 'required|exists:positions,id',

            'role' => 'required',

            'avatar' => 'nullable|image|max:2048'

        ];
    }
}
