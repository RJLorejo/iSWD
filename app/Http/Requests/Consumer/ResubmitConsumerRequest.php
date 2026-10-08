<?php

namespace App\Http\Requests\Consumer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResubmitConsumerRequest extends FormRequest
{
    /**
     * Determine whether the consumer is allowed
     * to correct and resubmit the registration.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user
            && $user->hasRole('Consumer')
            && $user->consumer
            && $user->consumer->verification_status === 'Rejected';
    }


    /**
     * Validation rules.
     */
    public function rules(): array
    {
        $consumer = $this->user()?->consumer;

        return [

            /*
            |--------------------------------------------------------------------------
            | Water Service Account
            |--------------------------------------------------------------------------
            */

            'account_number' => [
                'required',
                'string',
                'max:50',

                Rule::unique(
                    'consumers',
                    'account_number'
                )->ignore($consumer?->id),
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
            | Contact Information
            |--------------------------------------------------------------------------
            |
            | Philippine mobile number format:
            | 09XXXXXXXXX
            |
            | The current consumer is ignored when checking uniqueness
            | because this is an update/resubmission.
            |
            */

            'phone' => [
                'required',
                'string',
                'digits:11',
                'regex:/^09\d{9}$/',

                Rule::unique(
                    'consumers',
                    'phone'
                )->ignore($consumer?->id),
            ],


            /*
            |--------------------------------------------------------------------------
            | Service Address
            |--------------------------------------------------------------------------
            |
            | House / Building No. is optional.
            |
            | At least one of Street or Purok must be provided.
            | Both are also allowed when the actual address contains both.
            |
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
                'string',

                Rule::in(
                    config('sagay.barangays', [])
                ),
            ],


            /*
            |--------------------------------------------------------------------------
            | Registered Water Service Location
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
            | Confirmation
            |--------------------------------------------------------------------------
            */

            'terms' => [
                'required',
                'accepted',
            ],
        ];
    }


    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Account Number
            |--------------------------------------------------------------------------
            */

            'account_number.required' =>
                'Please enter your Sagay Water District account number.',

            'account_number.unique' =>
                'This water service account number is already registered to another consumer.',


            /*
            |--------------------------------------------------------------------------
            | Personal Information
            |--------------------------------------------------------------------------
            */

            'first_name.required' =>
                'Please enter the registered consumer first name.',

            'first_name.max' =>
                'First name must not exceed 100 characters.',

            'middle_name.max' =>
                'Middle name must not exceed 100 characters.',

            'last_name.required' =>
                'Please enter the registered consumer last name.',

            'last_name.max' =>
                'Last name must not exceed 100 characters.',

            'suffix.max' =>
                'Suffix must not exceed 20 characters.',

            'sex.required' =>
                'Please select your sex.',

            'sex.in' =>
                'Please select a valid sex.',


            /*
            |--------------------------------------------------------------------------
            | Phone
            |--------------------------------------------------------------------------
            */

            'phone.required' =>
                'Please enter your mobile number.',

            'phone.digits' =>
                'Mobile number must contain exactly 11 digits.',

            'phone.regex' =>
                'Mobile number must be a valid Philippine mobile number starting with 09.',

            'phone.unique' =>
                'This mobile number is already registered to another consumer.',


            /*
            |--------------------------------------------------------------------------
            | Address
            |--------------------------------------------------------------------------
            */

            'house_no.max' =>
                'House or building number must not exceed 100 characters.',

            'street.required_without' =>
                'Please enter either a street or purok.',

            'street.max' =>
                'Street must not exceed 150 characters.',

            'purok.required_without' =>
                'Please enter either a purok or street.',

            'purok.max' =>
                'Purok must not exceed 150 characters.',

            'barangay.required' =>
                'Please select your barangay.',

            'barangay.in' =>
                'Please select a valid barangay in Sagay City.',


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
                'The selected service location latitude is invalid.',

            'longitude.required' =>
                'Please select the registered water service location on the map.',

            'longitude.numeric' =>
                'The selected service location is invalid.',

            'longitude.between' =>
                'The selected service location longitude is invalid.',


            /*
            |--------------------------------------------------------------------------
            | Confirmation
            |--------------------------------------------------------------------------
            */

            'terms.required' =>
                'You must confirm that the corrected information is accurate.',

            'terms.accepted' =>
                'You must confirm that the corrected information is accurate.',
        ];
    }
}
