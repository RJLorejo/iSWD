<?php

namespace App\Http\Requests\Consumer;

use Illuminate\Foundation\Http\FormRequest;

class StoreConsumerComplaintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check()
            && auth()->user()->hasRole('Consumer');
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'subject' => trim($this->subject ?? ''),
            'description' => trim($this->description ?? ''),
            'address' => trim($this->address ?? ''),
            'landmark' => trim($this->landmark ?? ''),
        ]);
    }

    public function rules(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Complaint Category
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

            'subject' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'required',
                'string',
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

    public function messages(): array
    {
        return [

            'complaint_category_id.required' =>
            'Please select the type of concern you want to report.',

            'complaint_category_id.exists' =>
            'The selected complaint category is invalid.',

            'subject.required' =>
            'Please provide a short title for your concern.',

            'subject.max' =>
            'The subject must not exceed 255 characters.',

            'description.required' =>
            'Please describe what happened.',

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
