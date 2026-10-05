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

            'materials_parts' => [
                'nullable',
                'string',
                'max:10000',
            ],

            'technician_notes' => [
                'nullable',
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

    public function attributes(): array
    {
        return [
            'diagnosis' => 'diagnosis / findings',
            'root_cause' => 'root cause',
            'materials_parts' => 'materials / parts',
            'technician_notes' => 'plumber notes',
            'before_photo' => 'before photo',
            'after_photo' => 'after photo',
        ];
    }
}
