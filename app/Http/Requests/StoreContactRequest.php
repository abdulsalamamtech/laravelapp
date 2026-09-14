<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
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
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'other_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20'],
            'subject' => ['required', 'string', 'max:255'],
            'purpose' => ['sometimes', 'string'],
            'message' => ['required', 'string'],
            'organization' => ['sometimes', 'string'],
            // 'feedback' => ['nullable', 'string'],
            // 'status' => ['nullable', 'string', 'in:pending,reviewed,resolved'],
            // 'updated_by' => ['nullable', 'string', 'max:255'],
        ];
    }
}
