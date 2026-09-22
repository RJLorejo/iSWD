<?php

namespace App\Http\Requests\Consumer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateConsumerComplaintRequest extends FormRequest
{
    /**
     * Determine if the consumer is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check()
            && auth()->user()->hasRole('Consumer');
    }

    /**
     * Prepare input before validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'subject' => trim($this->subject ?? ''),
            'description' => trim($this->description ?? ''),
            'address' => trim($this->address ?? ''),
            'landmark' => trim($this->landmark ?? ''),
        ]);
    }

    /**
     * Validation rules.
     */
    public function rules(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Division
            |--------------------------------------------------------------------------
            */

            'division_id' => [
                'required',
                'integer',
                'exists:divisions,id',
            ],

            /*
            |--------------------------------------------------------------------------
            | Complaint Type
            |--------------------------------------------------------------------------
            */

            'complaint_category_id' => [
                'required',
                'integer',
                'exists:complaint_categories,id',
            ],

            /*
            |--------------------------------------------------------------------------
            | Complaint Information
            |--------------------------------------------------------------------------
            */

            'description' => [
                'required',
                'string',
                'min:10',
                'max:5000',
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
            | Supporting Photo
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
     * Validation messages.
     */
    public function messages(): array
    {
        return [

            'division_id.required' =>
                'Please select the division responsible for your concern.',

            'division_id.exists' =>
                'The selected division is invalid.',

            'complaint_category_id.required' =>
                'Please select the type of concern you want to report.',

            'complaint_category_id.exists' =>
                'The selected complaint type is invalid.',

            'description.required' =>
                'Please describe what happened.',

            'description.min' =>
                'Please provide at least 10 characters describing the concern.',

            'description.max' =>
                'The description must not exceed 5,000 characters.',

            'address.required' =>
                'Please provide the location where the problem occurred.',

            'address.max' =>
                'The address must not exceed 500 characters.',

            'landmark.max' =>
                'The landmark must not exceed 255 characters.',

            'latitude.numeric' =>
                'The map latitude must be a valid number.',

            'latitude.between' =>
                'The map latitude is outside the valid range.',

            'longitude.numeric' =>
                'The map longitude must be a valid number.',

            'longitude.between' =>
                'The map longitude is outside the valid range.',

            'photo.image' =>
                'The uploaded file must be an image.',

            'photo.mimes' =>
                'Please upload a JPG, JPEG, PNG, or WEBP image.',

            'photo.max' =>
                'The photo must not exceed 5 MB.',
        ];
    }
}
