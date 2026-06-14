<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'poli_id' => ['required', 'exists:polis,id'],
            'name' => ['required', 'string', 'max:255'],
            'specialty' => ['required', 'string', 'max:255'],
            'experience_years' => ['required', 'integer', 'min:0', 'max:50'],
            'daily_quota' => ['required', 'integer', 'min:1', 'max:50'],
        ];
    }
}
