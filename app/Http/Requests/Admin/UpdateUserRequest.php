<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized.
     */
    public function authorize(): bool
    {
        return true;
    }


    /**
     * Validation rules.
     */
    public function rules(): array
    {
        $user = $this->route('user');

        return [

            /*
            |--------------------------------------------------------------------------
            | Employee Information
            |--------------------------------------------------------------------------
            */

            'employee_id' => [
                'required',
                'string',
                'max:100',

                Rule::unique(
                    'users',
                    'employee_id'
                )->ignore($user->id),
            ],

            'first_name' => [
                'required',
                'string',
                'max:100',
            ],

            'middle_name' => [
                'nullable',
                'string',
                'max:100',
            ],

            'last_name' => [
                'required',
                'string',
                'max:100',
            ],

            'suffix' => [
                'nullable',
                'string',
                'max:20',
            ],


            /*
            |--------------------------------------------------------------------------
            | Contact Information
            |--------------------------------------------------------------------------
            */

            'email' => [
                'required',
                'email',
                'max:255',

                Rule::unique(
                    'users',
                    'email'
                )->ignore($user->id),
            ],

            'phone' => [
                'nullable',
                'string',
                'max:20',
            ],


            /*
            |--------------------------------------------------------------------------
            | Organization
            |--------------------------------------------------------------------------
            */

            'department_id' => [
                'required',
                'integer',
                Rule::exists(
                    'departments',
                    'id'
                ),
            ],

            'position_id' => [
                'required',
                'integer',
                Rule::exists(
                    'positions',
                    'id'
                ),
            ],


            /*
            |--------------------------------------------------------------------------
            | Employee Role
            |--------------------------------------------------------------------------
            |
            | Consumer cannot be assigned from Employee Management.
            |
            */

            'role' => [
                'required',
                'string',

                Rule::exists(
                    'roles',
                    'name'
                )->where(function ($query) {

                    $query->where(
                        'guard_name',
                        'web'
                    );
                }),

                Rule::notIn([
                    'Consumer',
                ]),
            ],


            /*
            |--------------------------------------------------------------------------
            | Profile Picture
            |--------------------------------------------------------------------------
            */

            'avatar' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],


            /*
            |--------------------------------------------------------------------------
            | Account Status
            |--------------------------------------------------------------------------
            */

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ];
    }


    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [

            'employee_id.required' =>
            'Employee ID is required.',

            'employee_id.unique' =>
            'This Employee ID is already assigned to another account.',

            'first_name.required' =>
            'First name is required.',

            'last_name.required' =>
            'Last name is required.',

            'email.required' =>
            'Email address is required.',

            'email.email' =>
            'Please enter a valid email address.',

            'email.unique' =>
            'This email address is already being used.',

            'department_id.required' =>
            'Please select a department.',

            'department_id.exists' =>
            'The selected department is invalid.',

            'position_id.required' =>
            'Please select a position.',

            'position_id.exists' =>
            'The selected position is invalid.',

            'role.required' =>
            'Please select an employee role.',

            'role.exists' =>
            'The selected employee role is invalid.',

            'role.not_in' =>
            'The Consumer role cannot be assigned through Employee Management.',

            'avatar.image' =>
            'The profile picture must be an image.',

            'avatar.mimes' =>
            'The profile picture must be a JPG, JPEG, PNG, or WEBP image.',

            'avatar.max' =>
            'The profile picture must not exceed 2 MB.',
        ];
    }
}
