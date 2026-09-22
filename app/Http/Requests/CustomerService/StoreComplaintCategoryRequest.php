<?php

namespace App\Http\Requests\CustomerService;

use Illuminate\Foundation\Http\FormRequest;

class StoreComplaintCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'division_id' => [
                'required',
                'integer',
                'exists:divisions,id',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
                'unique:complaint_categories,name',
            ],

            'description' => [
                'nullable',
                'string',
            ],

        ];
    }
}
