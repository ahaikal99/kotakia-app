<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($this->user())],
            'current_password' => ['required_with:password', 'nullable', 'current_password:web'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('email'))) {
            $this->merge(['email' => strtolower(trim($this->input('email')))]);
        }
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Sila masukkan nama.',
            'email.required' => 'Sila masukkan emel.',
            'email.email' => 'Sila masukkan emel yang sah.',
            'email.unique' => 'Emel ini telah digunakan oleh akaun lain.',
            'current_password.required_with' => 'Masukkan kata laluan semasa untuk menetapkan kata laluan baharu.',
            'current_password.current_password' => 'Kata laluan semasa tidak tepat.',
            'password.confirmed' => 'Pengesahan kata laluan baharu tidak sepadan.',
        ];
    }
}
