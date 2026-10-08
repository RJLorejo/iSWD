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
                'digits:11',
                'regex:/^09\d{9}$/',
                'unique:consumers,phone',
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
                'required_without:purok',
            ],

            'purok' => [
                'nullable',
                'string',
                'max:150',
                'required_without:street',
            ],

            'barangay' => [
                'required',
                Rule::in(config('sagay.barangays')),
            ],

            'latitude'  => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],


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
            'Mobile number is required.',

            'phone.digits' =>
            'Mobile number must contain exactly 11 digits.',

            'phone.regex' =>
            'Mobile number must be a valid Philippine number starting with 09.',

            'phone.unique' =>
            'This mobile number is already registered.',

            'email.required' =>
            'Please enter your email address.',

            'email.email' =>
            'Please enter a valid email address.',

            'email.unique' =>
            'This email address is already registered.',

            'street.required_without' =>
            'Please enter either a street or purok.',

            'purok.required_without' =>
            'Please enter either a purok or street.',

            'street.max' =>
            'Street must not exceed 150 characters.',

            'purok.max' =>
            'Purok must not exceed 150 characters.',

            'house_no.max' =>
            'House or building number must not exceed 100 characters.',

            'barangay.required' =>
            'Please enter your barangay.',

            'latitude.required' =>
            'Please select the registered water service location on the map.',

            'latitude.numeric' =>
            'The selected service location is invalid.',

            'longitude.required' =>
            'Please select the registered water service location on the map.',

            'longitude.numeric' =>
            'The selected service location is invalid.',

            'password.required' =>
            'Please create a password.',

            'password.confirmed' =>
            'Password confirmation does not match.',

            'terms.accepted' =>
            'You must confirm that the information provided is accurate.',
        ];
    }
}
