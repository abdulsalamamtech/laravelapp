<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateContactRequest extends FormRequest
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
            'first_name' => ['sometimes', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'other_name' => ['nullable', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255'],
            'phone_number' => ['sometimes', 'string', 'max:20'],
            'subject' => ['sometimes', 'string', 'max:255'],
            'purpose' => ['sometimes', 'string'],
            'organization' => ['required', 'string'],
            'message' => ['sometimes', 'string'],
            'feedback' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'in:pending,reviewed,resolved'],
            // 'updated_by' => ['nullable', 'string', 'max:255'],
        ];
    }
}
