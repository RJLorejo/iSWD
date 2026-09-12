<?php

namespace App\Http\Requests\Consumer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterConsumerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Account Information
            |--------------------------------------------------------------------------
            */

            'account_number' => [
                'required',
                'string',
                'max:50',
                'unique:consumers,account_number',
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

            'sex' => [
                'required',
                Rule::in([
                    'Male',
                    'Female',
                ]),
            ],

            'birth_date' => [
                'required',
                'date',
                'before:today',
            ],

            'phone' => [
                'required',
                'string',
                'max:20',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

            /*
            |--------------------------------------------------------------------------
            | Service Address
            |--------------------------------------------------------------------------
            */

            'house_no' => [
                'nullable',
                'string',
                'max:50',
            ],

            'street' => [
                'nullable',
                'string',
                'max:150',
            ],

            'purok' => [
                'nullable',
                'string',
                'max:100',
            ],

            'barangay' => [
                'required',
                'string',
                'max:100',
            ],

            'municipality' => [
                'required',
                'string',
                'max:100',
            ],

            'province' => [
                'required',
                'string',
                'max:100',
            ],
        ];
    }

    public function messages(): array
    {
        return [

            'account_number.unique' =>
            'This water service account number is already registered.',

            'email.unique' =>
            'This email address is already registered.',

            'birth_date.before' =>
            'Birth date must be earlier than today.',

            'password.confirmed' =>
            'Password confirmation does not match.',
        ];
    }
}
