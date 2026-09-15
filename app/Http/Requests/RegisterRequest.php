<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('phone'))) {
            $phone = preg_replace('/[\s()+-]/', '', $this->input('phone'));
            if (str_starts_with($phone, '0')) {
                $phone = '6'.$phone;
            }
            $this->merge(['phone' => $phone]);
        }
        if (is_string($this->input('email'))) {
            $this->merge(['email' => strtolower(trim($this->input('email')))]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^[1-9][0-9]{7,14}$/', 'unique:users,phone'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Sila masukkan nama penuh.',
            'phone.required' => 'Sila masukkan nombor telefon.',
            'phone.regex' => 'Masukkan nombor telefon yang sah, contohnya 0123456789.',
            'phone.unique' => 'Nombor telefon ini telah didaftarkan.',
            'email.required' => 'Sila masukkan emel.',
            'email.email' => 'Sila masukkan emel yang sah.',
            'email.unique' => 'Emel ini telah didaftarkan. Sila log masuk.',
            'password.required' => 'Sila masukkan kata laluan.',
            'password.min' => 'Kata laluan mesti sekurang-kurangnya 8 aksara.',
            'password.confirmed' => 'Pengesahan kata laluan tidak sepadan.',
        ];
    }
}
