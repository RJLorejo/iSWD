<?php

namespace App\Http\Requests\CustomerService;

use App\Models\Division;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreComplaintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'complainant_name' => $this->filled('complainant_name')
                ? trim((string) $this->complainant_name)
                : null,

            'complainant_phone' => $this->filled('complainant_phone')
                ? trim((string) $this->complainant_phone)
                : null,

            'description' => $this->filled('description')
                ? trim((string) $this->description)
                : null,

            'address' => $this->filled('address')
                ? trim((string) $this->address)
                : null,

            'landmark' => $this->filled('landmark')
                ? trim((string) $this->landmark)
                : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'consumer_id' => [
                'nullable',
                'integer',
                Rule::exists('consumers', 'id')
                    ->where(function ($query) {
                        $query->where('is_active', true);
                    }),
            ],

            'complainant_name' => [
                'required',
                'string',
                'max:255',
            ],

            'complainant_phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'division_id' => [
                'required',
                'integer',
                Rule::exists('divisions', 'id')
                    ->where(function ($query) {
                        $query->where('is_active', true);
                    }),
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
                Rule::requiredIf(function () {
                    $division = Division::query()
                        ->find($this->input('division_id'));

                    if (!$division) {
                        return false;
                    }

                    $divisionName = strtolower(
                        trim((string) $division->name)
                    );

                    $isEngineering = str_contains(
                        $divisionName,
                        'engineering'
                    );

                    $isCommercial = str_contains(
                        $divisionName,
                        'commercial'
                    );

                    if ($isEngineering) {
                        return true;
                    }

                    if (
                        $isCommercial &&
                        !$this->filled('consumer_id')
                    ) {
                        return true;
                    }

                    return false;
                }),
                'nullable',
                'string',
                'max:1000',
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

    public function messages(): array
    {
        return [
            'consumer_id.exists' =>
                'The selected SWD account is invalid or inactive.',

            'complainant_name.required' =>
                'Please enter the name of the person reporting the complaint.',

            'division_id.required' =>
                'Please select a division.',

            'division_id.exists' =>
                'The selected division is invalid or inactive.',

            'complaint_category_id.required' =>
                'Please select a complaint type.',

            'complaint_category_id.exists' =>
                'The selected complaint type does not belong to the selected division or is inactive.',

            'description.required' =>
                'Please provide a description of the complaint.',

            'description.min' =>
                'The complaint description must contain at least 10 characters.',

            'address.required' =>
                'Please provide the service address for an Engineering complaint or the complainant address for a Commercial complaint without a linked SWD account.',

            'photo.image' =>
                'The supporting file must be an image.',

            'photo.mimes' =>
                'The supporting image must be a JPG, JPEG, PNG, or WEBP file.',

            'photo.max' =>
                'The supporting image must not exceed 5 MB.',
        ];
    }
}
