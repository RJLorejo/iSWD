<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateConsumerRequest extends FormRequest
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
                Rule::in(['Male', 'Female']),
            ],

            'birth_date' => [
                'nullable',
                'date',
            ],

            'phone' => [
                'required',
                'string',
                'max:20',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
                'required_if:create_account,1',
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
                'nullable',
                'string',
                'max:255',
            ],

            'province' => [
                'nullable',
                'string',
                'max:255',
            ],

            'zip_code' => [
                'nullable',
                'string',
                'max:20',
            ],


            /*
            |--------------------------------------------------------------------------
            | Service Connection
            |--------------------------------------------------------------------------
            */

            'account_number' => [
                'required',
                'string',
                'max:100',
            ],

            'meter_number' => [
                'required',
                'string',
                'max:100',
            ],

            'connection_type' => [
                'required',
                Rule::in([
                    'Residential',
                    'Commercial',
                    'Government',
                    'Institutional',
                ]),
            ],

            'meter_size' => [
                'nullable',
                'string',
                'max:50',
            ],

            /*
             * IMPORTANT:
             *
             * The form uses connection_status.
             * The database column is status.
             */
            'connection_status' => [
                'required',
                Rule::in([
                    'Active',
                    'Inactive',
                    'Disconnected',
                    'Temporary',
                    'Pending',
                ]),
            ],

            'installation_date' => [
                'nullable',
                'date',
            ],

            'remarks' => [
                'nullable',
                'string',
            ],


            /*
            |--------------------------------------------------------------------------
            | Service Address
            |--------------------------------------------------------------------------
            */

            'service_house_no' => [
                'nullable',
                'string',
                'max:100',
            ],

            'service_street' => [
                'nullable',
                'string',
                'max:255',
            ],

            'service_purok' => [
                'nullable',
                'string',
                'max:100',
            ],

            'service_barangay' => [
                'required',
                'string',
                'max:255',
            ],

            /*
             * IMPORTANT:
             *
             * The form uses service_city.
             * The database column is city.
             */
            'service_city' => [
                'required',
                'string',
                'max:255',
            ],

            'service_province' => [
                'required',
                'string',
                'max:255',
            ],

            'service_postal_code' => [
                'nullable',
                'string',
                'max:20',
            ],

            'landmark' => [
                'nullable',
                'string',
                'max:255',
            ],


            /*
            |--------------------------------------------------------------------------
            | Portal Account
            |--------------------------------------------------------------------------
            */

            'create_account' => [
                'nullable',
                'boolean',
            ],

        ];
    }

    public function messages(): array
    {
        return [

            'connection_status.required' =>
            'Please select the service connection status.',

            'service_city.required' =>
            'Please enter the service city.',

            'service_barangay.required' =>
            'Please enter the service barangay.',

            'service_province.required' =>
            'Please enter the service province.',

            'email.required_if' =>
            'An email address is required when creating a consumer portal account.',

        ];
    }
}
