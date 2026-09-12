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
     * Validation rules.
     */
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

            'photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ];
    }
}
