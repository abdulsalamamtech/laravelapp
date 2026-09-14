<?php

namespace App\Http\Requests\Quest;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateWaitlistRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['nullable', 'max:255'],
            'source' => ['nullable', 'max:255'],
            'name' => ['nullable', 'max:255'],
            'email' => ['required', 'max:255', 'email'],
            'status' => ['required', 'max:255', 'in:pending,invited,rejected'],
        ];
    }
}
