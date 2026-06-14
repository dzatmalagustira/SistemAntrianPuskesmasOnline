<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'poli_id' => ['required', 'exists:polis,id'],
            'doctor_id' => ['required', 'exists:doctors,id'],
            'visit_date' => ['required', 'date', 'after_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
