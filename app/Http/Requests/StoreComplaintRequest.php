<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreComplaintRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare request data before validation.
     */
    protected function prepareForValidation(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Normalize complainant type
        |--------------------------------------------------------------------------
        */

        $complainantType = $this->input('complainant_type');

        /*
        |--------------------------------------------------------------------------
        | If registered consumer, clear walk-in information.
        |--------------------------------------------------------------------------
        */

        if ($complainantType === 'registered') {
            $this->merge([
                'complainant_name' => null,
                'complainant_phone' => null,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | If walk-in, consumer_id must be null.
        |--------------------------------------------------------------------------
        */

        if ($complainantType === 'walk_in') {
            $this->merge([
                'consumer_id' => null,
            ]);
        }
    }

    /**
     * Validation rules.
     */
    public function rules(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Complainant
            |--------------------------------------------------------------------------
            */

            'complainant_type' => [
                'required',
                Rule::in([
                    'registered',
                    'walk_in',
                ]),
            ],

            'consumer_id' => [
                'nullable',
                'integer',
                'exists:consumers,id',
                'required_if:complainant_type,registered',
            ],

            'complainant_name' => [
                'nullable',
                'string',
                'max:255',
                'required_if:complainant_type,walk_in',
            ],

            'complainant_phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            /*
            |--------------------------------------------------------------------------
            | Complaint Classification
            |--------------------------------------------------------------------------
            */

            'complaint_category_id' => [
                'required',
                'integer',
                'exists:complaint_categories,id',
            ],

            'priority' => [
                'required',
                Rule::in([
                    'Low',
                    'Medium',
                    'High',
                    'Critical',
                ]),
            ],

            /*
            |--------------------------------------------------------------------------
            | Complaint Information
            |--------------------------------------------------------------------------
            */

            'subject' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'required',
                'string',
            ],

            /*
            |--------------------------------------------------------------------------
            | Location
            |--------------------------------------------------------------------------
            */

            'address' => [
                'required',
                'string',
                'max:500',
            ],

            'landmark' => [
                'nullable',
                'string',
                'max:255',
            ],

            'latitude' => [
                'nullable',
                'numeric',
                'between:-90,90',
            ],

            'longitude' => [
                'nullable',
                'numeric',
                'between:-180,180',
            ],

            /*
            |--------------------------------------------------------------------------
            | Photo
            |--------------------------------------------------------------------------
            */

            'photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
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
            | Complainant
            |--------------------------------------------------------------------------
            */

            'complainant_type.required' =>
            'Please select the type of complainant.',

            'complainant_type.in' =>
            'The selected complainant type is invalid.',

            'consumer_id.required_if' =>
            'Please select a registered consumer.',

            'consumer_id.exists' =>
            'The selected consumer does not exist.',

            'complainant_name.required_if' =>
            'Please enter the full name of the walk-in complainant.',

            'complainant_name.string' =>
            'The complainant name must be a valid text.',

            'complainant_name.max' =>
            'The complainant name must not exceed 255 characters.',

            'complainant_phone.string' =>
            'The complainant contact number must be valid.',

            'complainant_phone.max' =>
            'The complainant contact number must not exceed 30 characters.',

            /*
            |--------------------------------------------------------------------------
            | Complaint Category
            |--------------------------------------------------------------------------
            */

            'complaint_category_id.required' =>
            'Please select a complaint category.',

            'complaint_category_id.exists' =>
            'The selected complaint category does not exist.',

            /*
            |--------------------------------------------------------------------------
            | Priority
            |--------------------------------------------------------------------------
            */

            'priority.required' =>
            'Please select the complaint priority.',

            'priority.in' =>
            'The selected complaint priority is invalid.',

            /*
            |--------------------------------------------------------------------------
            | Complaint Information
            |--------------------------------------------------------------------------
            */

            'subject.required' =>
            'Please enter a complaint subject.',

            'subject.max' =>
            'The complaint subject must not exceed 255 characters.',

            'description.required' =>
            'Please describe the reported problem.',

            /*
            |--------------------------------------------------------------------------
            | Location
            |--------------------------------------------------------------------------
            */

            'address.required' =>
            'Please provide the location of the reported problem.',

            'address.max' =>
            'The problem address must not exceed 500 characters.',

            'landmark.max' =>
            'The landmark must not exceed 255 characters.',

            'latitude.numeric' =>
            'The latitude must be a valid number.',

            'latitude.between' =>
            'The latitude must be between -90 and 90.',

            'longitude.numeric' =>
            'The longitude must be a valid number.',

            'longitude.between' =>
            'The longitude must be between -180 and 180.',

            /*
            |--------------------------------------------------------------------------
            | Photo
            |--------------------------------------------------------------------------
            */

            'photo.image' =>
            'The uploaded file must be an image.',

            'photo.mimes' =>
            'The complaint photo must be JPG, JPEG, PNG, or WEBP.',

            'photo.max' =>
            'The complaint photo must not exceed 5 MB.',
        ];
    }
}
