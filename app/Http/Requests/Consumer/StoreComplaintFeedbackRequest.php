<?php

namespace App\Http\Requests\Consumer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreComplaintFeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'overall_rating' => [
                'required',
                'integer',
                'between:1,5',
            ],

            'service_quality_rating' => [
                'required',
                'integer',
                'between:1,5',
            ],

            'response_time_rating' => [
                'required',
                'integer',
                'between:1,5',
            ],

            'personnel_courtesy_rating' => [
                'required',
                'integer',
                'between:1,5',
            ],

            'resolution_status' => [
                'required',
                Rule::in([
                    'Resolved',
                    'Partially Resolved',
                    'Not Resolved',
                ]),
            ],

            'comments' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'overall_rating.required' => 'Please provide an overall service rating.',
            'overall_rating.between' => 'The overall service rating must be between 1 and 5.',

            'service_quality_rating.required' => 'Please rate the service quality.',
            'service_quality_rating.between' => 'The service quality rating must be between 1 and 5.',

            'response_time_rating.required' => 'Please rate the response time.',
            'response_time_rating.between' => 'The response time rating must be between 1 and 5.',

            'personnel_courtesy_rating.required' => 'Please rate the courtesy of the service personnel.',
            'personnel_courtesy_rating.between' => 'The personnel courtesy rating must be between 1 and 5.',

            'resolution_status.required' => 'Please indicate whether your concern was resolved.',
            'resolution_status.in' => 'Please select a valid resolution status.',

            'comments.max' => 'Your comments may not exceed 2,000 characters.',
        ];
    }
}
