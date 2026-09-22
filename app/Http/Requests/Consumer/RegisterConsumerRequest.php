<?php

namespace App\Http\Requests\Consumer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

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
            | SWD Account
            |--------------------------------------------------------------------------
            */

            'account_number' => [
                'required',
                'string',
                'max:50',

                Rule::unique(
                    'consumers',
                    'account_number'
                ),
            ],


            /*
            |--------------------------------------------------------------------------
            | Personal Information
            |--------------------------------------------------------------------------
            */

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


            /*
            |--------------------------------------------------------------------------
            | Contact
            |--------------------------------------------------------------------------
            */

            'phone' => [
                'required',
                'string',
                'max:20',
            ],

            'email' => [
                'required',
                'email',
                'max:255',

                Rule::unique(
                    'users',
                    'email'
                ),
            ],


            /*
            |--------------------------------------------------------------------------
            | Address
            |--------------------------------------------------------------------------
            */

            'house_no' => [
                'nullable',
                'string',
                'max:100',
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
                'max:150',
            ],


            /*
            |--------------------------------------------------------------------------
            | Password
            |--------------------------------------------------------------------------
            */

            'password' => [
                'required',
                'confirmed',

                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers(),
            ],


            /*
            |--------------------------------------------------------------------------
            | Agreement
            |--------------------------------------------------------------------------
            */

            'terms' => [
                'required',
                'accepted',
            ],
        ];
    }


    public function messages(): array
    {
        return [

            'account_number.required' =>
            'Please enter your Sagay Water District account number.',

            'account_number.unique' =>
            'This water service account number is already registered.',

            'first_name.required' =>
            'Please enter the registered consumer first name.',

            'last_name.required' =>
            'Please enter the registered consumer last name.',

            'sex.required' =>
            'Please select your sex.',

            'phone.required' =>
            'Please enter your mobile number.',

            'email.required' =>
            'Please enter your email address.',

            'email.email' =>
            'Please enter a valid email address.',

            'email.unique' =>
            'This email address is already registered.',

            'barangay.required' =>
            'Please enter your barangay.',

            'password.required' =>
            'Please create a password.',

            'password.confirmed' =>
            'Password confirmation does not match.',

            'terms.accepted' =>
            'You must confirm that the information provided is accurate.',
        ];
    }
}
