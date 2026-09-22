<?php

namespace App\Http\Requests\CustomerService;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateComplaintCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $complaintCategory = $this->route('complaint_category');

        return [
            'division_id' => [
                'required',
                'integer',
                Rule::exists('divisions', 'id')
                    ->where(function ($query) {
                        $query->where('is_active', true);
                    }),
            ],

            'name' => [
                'required',
                'string',
                'max:255',

                Rule::unique('complaint_categories', 'name')
                    ->ignore($complaintCategory->id),
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ];
    }
}
