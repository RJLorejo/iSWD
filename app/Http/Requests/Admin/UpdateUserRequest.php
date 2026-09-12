<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->route('user');

        return [

            'employee_id' => [
                'required',
                Rule::unique('users')->ignore($user->id),
            ],

            'first_name' => 'required|string|max:255',

            'middle_name' => 'nullable|string|max:255',

            'last_name' => 'required|string|max:255',

            'suffix' => 'nullable|string|max:50',

            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],

            'phone' => 'nullable|string|max:20',

            'department_id' => 'required|exists:departments,id',

            'position_id' => 'required|exists:positions,id',

            'role' => 'required|exists:roles,name',

            'avatar' => 'nullable|image|max:2048',

            'is_active' => 'nullable|boolean',
        ];
    }
}
