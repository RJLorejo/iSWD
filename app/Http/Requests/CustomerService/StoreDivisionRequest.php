<?php

namespace App\Http\Requests\CustomerService;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDivisionRequest extends FormRequest
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
            'name' => trim((string) $this->input('name')),
            'description' => $this->filled('description')
                ? trim((string) $this->input('description'))
                : null,
        ]);
    }


    /**
     * Validation rules.
     */
    public function rules(): array
    {
        return [

            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('divisions', 'name'),
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
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

        ];
    }
}
