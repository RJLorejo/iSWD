<?php

namespace App\Http\Requests\Technician;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaintenanceReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check()
            && auth()->user()->hasRole('Maintenance Technician');
    }

    public function rules(): array
    {
        return [
            'diagnosis' => [
                'required',
                'string',
                'max:5000',
            ],

            'root_cause' => [
                'required',
                'string',
                'max:5000',
            ],

            'work_performed' => [
                'required',
                'string',
                'max:10000',
            ],

            'repair_procedure' => [
                'required',
                'string',
                'max:10000',
            ],

            'materials_used' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'parts_replaced' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'tools_used' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'technician_notes' => [
                'nullable',
                'string',
                'max:10000',
            ],

            'completion_remarks' => [
                'required',
                'string',
                'max:10000',
            ],

            'before_photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'after_photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ];
    }
}
