<?php

namespace App\Http\Requests\Technician;

use Illuminate\Foundation\Http\FormRequest;

class CompleteComplaintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check()
            && auth()->user()->hasRole('Maintenance Technician');
    }

    public function rules(): array
    {
        return [];
    }
}
