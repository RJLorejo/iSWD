<?php

namespace App\Http\Requests\Consumer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResubmitConsumerRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user
            && $user->hasRole('Consumer')
            && $user->consumer
            && $user->consumer->verification_status === 'Rejected';
    }

    public function rules(): array
    {
        $consumer = $this->user()?->consumer;

        return [
            'account_number' => [
                'required',
                'string',
                'max:50',

                Rule::unique(
                    'consumers',
                    'account_number'
                )->ignore($consumer?->id),
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
            'This water service account number is already registered to another consumer.',

            'first_name.required' =>
            'Please enter the registered consumer first name.',

            'last_name.required' =>
            'Please enter the registered consumer last name.',

            'sex.required' =>
            'Please select your sex.',

            'phone.required' =>
            'Please enter your mobile number.',

            'barangay.required' =>
            'Please enter your barangay.',

            'terms.accepted' =>
            'You must confirm that the corrected information is accurate.',
        ];
    }
}
