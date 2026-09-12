<?php

namespace App\Http\Requests\Consumer;

use Illuminate\Foundation\Http\FormRequest;

class StoreComplaintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check()
            && auth()->user()->hasRole('Consumer');
    }

    public function rules(): array
    {
        return [

            'complaint_category_id' => [
                'required',
                'integer',
                'exists:complaint_categories,id',
            ],

            'subject' => [
                'required',
                'string',
                'max:255',
            ],

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

    public function messages(): array
    {
        return [

            'complaint_category_id.required' =>
                'Please select the type of concern.',

            'complaint_category_id.exists' =>
                'The selected concern category is invalid.',

            'subject.required' =>
                'Please provide a short subject for your concern.',

            'subject.max' =>
                'The subject must not exceed 255 characters.',

            'description.required' =>
                'Please describe what happened.',

            'description.min' =>
                'Please provide a little more information about the problem.',

            'description.max' =>
                'The description must not exceed 5,000 characters.',

            'address.required' =>
                'Please provide the location of the reported problem.',

            'address.max' =>
                'The address must not exceed 500 characters.',

            'landmark.max' =>
                'The landmark must not exceed 255 characters.',

            'latitude.numeric' =>
                'The selected map location is invalid.',

            'latitude.between' =>
                'The latitude must be between -90 and 90.',

            'longitude.numeric' =>
                'The selected map location is invalid.',

            'longitude.between' =>
                'The longitude must be between -180 and 180.',

            'photo.image' =>
                'The uploaded file must be an image.',

            'photo.mimes' =>
                'The photo must be JPG, JPEG, PNG, or WEBP.',

            'photo.max' =>
                'The photo must not exceed 5 MB.',

        ];
    }
}
