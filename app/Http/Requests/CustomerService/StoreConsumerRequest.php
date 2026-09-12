<?php

namespace App\Http\Requests\CustomerService;

use Illuminate\Foundation\Http\FormRequest;

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
            | Consumer
            |--------------------------------------------------------------------------
            */

            'first_name' => [
                'required',
                'string',
                'max:255',
            ],

            'middle_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'last_name' => [
                'required',
                'string',
                'max:255',
            ],

            'suffix' => [
                'nullable',
                'string',
                'max:20',
            ],

            'sex' => [
                'required',
                'in:Male,Female',
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

            'zip_code' => [
                'nullable',
                'string',
                'max:20',
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


            /*
            |--------------------------------------------------------------------------
            | Service Connection
            |--------------------------------------------------------------------------
            */

            'account_number' => [
                'required',
                'string',
                'max:255',
            ],

            'service_connection_number' => [
                'nullable',
                'string',
                'max:255',
            ],

            'meter_number' => [
                'required',
                'string',
                'max:255',
            ],

            'connection_type' => [
                'required',
                'in:Residential,Commercial,Government,Institutional',
            ],

            'meter_size' => [
                'nullable',
                'string',
                'max:100',
            ],

            'status' => [
                'required',
                'in:Active,Inactive,Disconnected,Temporary,Pending',
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

            'service_zip_code' => [
                'nullable',
                'string',
                'max:20',
            ],

            'landmark' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }
}
