<?php

namespace App\Http\Requests\CustomerService;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreConsumerRequest extends FormRequest
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
            | Consumer Information
            |--------------------------------------------------------------------------
            */

            'account_number' => [
                'required',
                'string',
                'max:100',
                Rule::unique('consumers', 'account_number'),
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

            /*
            |--------------------------------------------------------------------------
            | Philippine Mobile Number
            |--------------------------------------------------------------------------
            */

            'phone' => [
                'required',
                'string',
                'digits:11',
                'regex:/^09\d{9}$/',
            ],

            /*
            |--------------------------------------------------------------------------
            | Portal Login Email
            |--------------------------------------------------------------------------
            */

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
            ],

            /*
            |--------------------------------------------------------------------------
            | Registered Service Address
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
                'max:255',
            ],

            'purok' => [
                'nullable',
                'string',
                'max:100',
            ],

            'barangay' => [
                'required',
                'string',
                'max:255',
            ],

            'municipality' => [
                'required',
                'string',
                'max:255',
            ],

            'province' => [
                'required',
                'string',
                'max:255',
            ],

            /*
            |--------------------------------------------------------------------------
            | Registered Service Coordinates
            |--------------------------------------------------------------------------
            */

            'latitude' => [
                'required',
                'numeric',
                'between:-90,90',
            ],

            'longitude' => [
                'required',
                'numeric',
                'between:-180,180',
            ],

            /*
            |--------------------------------------------------------------------------
            | Customer Service Certification
            |--------------------------------------------------------------------------
            */

            'information_certified' => [
                'required',
                'accepted',
            ],

        ];
    }

    public function messages(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Consumer Information
            |--------------------------------------------------------------------------
            */

            'account_number.required' =>
            'Please enter the consumer account number.',

            'account_number.unique' =>
            'This consumer account number is already registered.',

            'first_name.required' =>
            'Please enter the consumer first name.',

            'last_name.required' =>
            'Please enter the consumer last name.',

            'sex.required' =>
            'Please select the consumer sex.',

            'sex.in' =>
            'Please select a valid sex.',

            /*
            |--------------------------------------------------------------------------
            | Phone Number
            |--------------------------------------------------------------------------
            */

            'phone.required' =>
            'Please enter the consumer phone number.',

            'phone.digits' =>
            'The phone number must contain exactly 11 digits.',

            'phone.regex' =>
            'Enter a valid Philippine mobile number beginning with 09.',

            /*
            |--------------------------------------------------------------------------
            | Portal Email
            |--------------------------------------------------------------------------
            */

            'email.required' =>
            'Please enter an email address for the consumer portal account.',

            'email.email' =>
            'Please enter a valid email address.',

            'email.unique' =>
            'This email address is already registered to another account.',

            /*
            |--------------------------------------------------------------------------
            | Service Address
            |--------------------------------------------------------------------------
            */

            'barangay.required' =>
            'Please enter the barangay.',

            'municipality.required' =>
            'Please enter the municipality.',

            'province.required' =>
            'Please enter the province.',

            /*
            |--------------------------------------------------------------------------
            | Map Location
            |--------------------------------------------------------------------------
            */

            'latitude.required' =>
            'Please select the registered water service location on the map.',

            'latitude.numeric' =>
            'The selected service location is invalid.',

            'latitude.between' =>
            'The selected latitude is invalid.',

            'longitude.required' =>
            'Please select the registered water service location on the map.',

            'longitude.numeric' =>
            'The selected service location is invalid.',

            'longitude.between' =>
            'The selected longitude is invalid.',

            /*
            |--------------------------------------------------------------------------
            | Certification
            |--------------------------------------------------------------------------
            */

            'information_certified.required' =>
            'Please complete the consumer information certification.',

            'information_certified.accepted' =>
            'You must certify that the consumer information has been reviewed before registration.',

        ];
    }
}
