<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PromotionBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isManager() && $this->user()->is_active;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'poster' => [$this->isMethod('POST') ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:max_width=6000,max_height=6000'],
            'expires_on' => ['required', 'date_format:Y-m-d', 'after_or_equal:'.now('Asia/Kuala_Lumpur')->format('Y-m-d')],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Sila masukkan tajuk promosi.',
            'title.max' => 'Tajuk maksimum 150 aksara.',
            'poster.required' => 'Sila muat naik poster.',
            'poster.image' => 'Poster mesti fail imej yang sah.',
            'poster.mimes' => 'Gunakan poster JPG, PNG atau WebP.',
            'poster.max' => 'Saiz poster maksimum 2 MB.',
            'poster.dimensions' => 'Dimensi poster maksimum 6000 × 6000 piksel.',
            'expires_on.required' => 'Sila pilih tarikh luput.',
            'expires_on.date_format' => 'Tarikh luput tidak sah.',
            'expires_on.after_or_equal' => 'Tarikh luput mestilah hari ini atau selepasnya.',
        ];
    }
}
