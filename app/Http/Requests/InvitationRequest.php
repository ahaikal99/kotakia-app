<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InvitationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'theme' => ['required', Rule::in(array_keys(config('catalog.themes')))],
            'host_name' => ['required', 'string', 'max:255'],
            'celebrant_name' => ['required', 'string', 'max:255'],
            'event_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
            'venue' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:2000'],
            'map_url' => ['nullable', 'url:http,https', 'max:2048'],
            'contact_name' => ['required', 'string', 'max:255'],
            'contact_phone' => ['required', 'string', 'max:25', 'regex:/^\+?[0-9][0-9\s()-]{7,24}$/'],
            'message' => ['nullable', 'string', 'max:3000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'tajuk majlis', 'theme' => 'tema', 'host_name' => 'nama tuan rumah',
            'celebrant_name' => 'nama yang diraikan', 'event_date' => 'tarikh majlis',
            'start_time' => 'masa mula', 'end_time' => 'masa tamat', 'venue' => 'nama lokasi',
            'address' => 'alamat', 'map_url' => 'pautan peta', 'contact_name' => 'nama untuk dihubungi',
            'contact_phone' => 'telefon untuk dihubungi', 'message' => 'ucapan jemputan',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Sila isi :attribute.',
            'max' => ':Attribute terlalu panjang (maksimum :max aksara).',
            'event_date.after_or_equal' => 'Tarikh majlis mesti hari ini atau selepasnya.',
            'event_date.date_format' => 'Sila pilih tarikh majlis yang sah.',
            'end_time.after' => 'Masa tamat mesti selepas masa mula pada hari yang sama.',
            'date_format' => 'Format :attribute tidak sah.',
            'map_url.url' => 'Pautan peta mesti bermula dengan https:// atau http://.',
            'contact_phone.regex' => 'Sila masukkan nombor telefon yang sah.',
            'theme.in' => 'Sila pilih tema yang tersedia.',
        ];
    }
}
