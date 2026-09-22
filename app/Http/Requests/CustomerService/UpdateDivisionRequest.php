<?php

namespace App\Http\Requests\CustomerService;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDivisionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized.
     */
    public function authorize(): bool
    {
        return true;
    }


    /**
     * Prepare data before validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([

            'name' => trim(
                (string) $this->input('name')
            ),

            'description' => $this->filled('description')
                ? trim((string) $this->input('description'))
                : null,

            'is_active' => $this->boolean('is_active'),

        ]);
    }


    /**
     * Validation rules.
     */
    public function rules(): array
    {
        $division = $this->route('division');

        return [

            'name' => [
                'required',
                'string',
                'max:255',

                Rule::unique(
                    'divisions',
                    'name'
                )->ignore($division?->id),
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],

        ];
    }


    /**
     * Validation messages.
     */
    public function messages(): array
    {
        return [

            'name.required' =>
                'Please enter the division name.',

            'name.string' =>
                'The division name must be valid text.',

            'name.max' =>
                'The division name must not exceed 255 characters.',

            'name.unique' =>
                'A division with this name already exists.',

            'description.string' =>
                'The description must be valid text.',

            'description.max' =>
                'The description must not exceed 1000 characters.',

            'is_active.required' =>
                'Please select the division status.',

            'is_active.boolean' =>
                'The selected division status is invalid.',

        ];
    }
}
