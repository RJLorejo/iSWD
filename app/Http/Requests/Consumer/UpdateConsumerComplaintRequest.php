<?php

namespace App\Http\Requests\Consumer;

use App\Models\Division;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateConsumerComplaintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check()
            && auth()->user()->hasRole('Consumer');
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'description' => trim($this->description ?? ''),
            'address' => trim($this->address ?? ''),
            'landmark' => trim($this->landmark ?? ''),
        ]);
    }

    public function rules(): array
    {
        $engineeringDivision = Division::query()
            ->where('name', 'Engineering Operation')
            ->first();

        $isEngineering = $engineeringDivision
            && (int) $this->input('division_id') === (int) $engineeringDivision->id;

        return [
            'division_id' => [
                'required',
                'integer',
                Rule::exists('divisions', 'id')
                    ->where('is_active', true),
            ],

            'complaint_category_id' => [
                'required',
                'integer',
                Rule::exists('complaint_categories', 'id')
                    ->where(function ($query) {
                        $query
                            ->where('is_active', true)
                            ->where(
                                'division_id',
                                $this->input('division_id')
                            );
                    }),
            ],

            'description' => [
                'required',
                'string',
                'min:10',
                'max:5000',
            ],

            'address' => [
                $isEngineering ? 'required' : 'nullable',
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

            'photos' => [
                'nullable',
                'array',
                'max:5',
            ],

            'photos.*' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'remove_photos' => [
                'nullable',
                'array',
            ],

            'remove_photos.*' => [
                'integer',
                Rule::exists('complaint_photos', 'id')
                    ->where(function ($query) {
                        $complaint = $this->route('complaint');

                        if ($complaint) {
                            $query->where(
                                'complaint_id',
                                $complaint->id
                            );
                        }
                    }),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'division_id.required' =>
                'Please select the division responsible for your concern.',

            'division_id.exists' =>
                'The selected division is invalid or inactive.',

            'complaint_category_id.required' =>
                'Please select the type of concern you want to report.',

            'complaint_category_id.exists' =>
                'The selected complaint type does not belong to the selected division or is no longer available.',

            'description.required' =>
                'Please describe your concern.',

            'description.min' =>
                'Please provide at least 10 characters describing your concern.',

            'description.max' =>
                'The description must not exceed 5,000 characters.',

            'address.required' =>
                'Please provide the location where the reported concern occurred.',

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

            'photos.array' =>
                'The supporting photos must be valid files.',

            'photos.max' =>
                'You may upload a maximum of 5 supporting photos.',

            'photos.*.required' =>
                'Each supporting photo is required.',

            'photos.*.image' =>
                'Each supporting file must be an image.',

            'photos.*.mimes' =>
                'Supporting photos must be JPG, JPEG, PNG, or WEBP.',

            'photos.*.max' =>
                'Each supporting photo must not exceed 5 MB.',

            'remove_photos.array' =>
                'The selected photos to remove are invalid.',

            'remove_photos.*.integer' =>
                'The selected photo to remove is invalid.',

            'remove_photos.*.exists' =>
                'One of the selected photos could not be found.',
        ];
    }
}
