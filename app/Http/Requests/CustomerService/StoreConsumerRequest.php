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

                Rule::unique(
                    'consumers',
                    'account_number'
                ),
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

            'phone' => [
                'required',
                'string',
                'max:20',
            ],

            /*
            |--------------------------------------------------------------------------
            | Portal Login Email
            |--------------------------------------------------------------------------
            |
            | Every consumer created by Customer Service now receives
            | an online portal account.
            |
            */

            'email' => [
                'required',
                'email',
                'max:255',

                /*
                 * Do NOT exclude soft-deleted users here.
                 *
                 * The database unique constraint still sees those rows,
                 * so validation must also see them.
                 */
                Rule::unique(
                    'users',
                    'email'
                ),
            ],


            /*
            |--------------------------------------------------------------------------
            | Residential Address
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

        ];
    }


    public function messages(): array
    {
        return [

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

            'phone.required' =>
                'Please enter the consumer phone number.',

            'email.required' =>
                'Please enter an email address for the consumer portal account.',

            'email.email' =>
                'Please enter a valid email address.',

            'email.unique' =>
                'This email address is already registered to another account.',

            'barangay.required' =>
                'Please enter the barangay.',

            'municipality.required' =>
                'Please enter the municipality.',

            'province.required' =>
                'Please enter the province.',
        ];
    }
}
