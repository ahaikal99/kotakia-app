@extends('layouts.customer')
@section('title', 'Maklumat Kad')
@section('content')
<div class="max-w-4xl mx-auto">
    <a href="{{ route('dashboard') }}" class="text-sm text-slate-500 hover:text-[#92743e]">← Dashboard</a>
    <div class="mt-6 mb-8"><span class="eyebrow">{{ $invitation->exists ? 'Edit jemputan anda' : 'Cipta jemputan anda' }}</span><h1 class="font-serif text-3xl md:text-4xl mt-4">Ceritakan tentang <span class="text-[#B6975A] italic">majlis anda.</span></h1><p class="text-slate-500 mt-3">Maklumat ini akan digunakan di dalam kad. Medan bertanda * wajib diisi.</p></div>
    <x-order-steps :step="1" />
    <form method="POST" action="{{ $invitation->exists ? route('invitations.update', $invitation) : route('invitations.store') }}" class="space-y-6">
        @csrf
        @if ($invitation->exists) @method('PUT') @endif
        <section class="panel">
            <h2 class="text-xl font-bold mb-6">01 &nbsp; Maklumat Majlis</h2>
            <div class="grid sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2"><x-form-field name="title" label="Tajuk majlis" :value="$invitation->title" required maxlength="255" placeholder="Majlis Perkahwinan Arif & Najihah" /></div>
                <div>
                    <label for="theme" class="block text-sm font-semibold mb-2">Tema majlis <span class="text-[#92743e]">*</span></label>
                    <select name="theme" id="theme" class="form-input" required>
                        <option value="">Pilih tema</option>
                        @foreach ($themes as $code => $label)<option value="{{ $code }}" @selected(old('theme', $invitation->theme) === $code)>{{ $label }}</option>@endforeach
                    </select>
                    <p class="text-xs text-slate-500 mt-2">Reka bentuk tersedia untuk Perkahwinan. Tema lain boleh disimpan sebagai draf.</p>
                </div>
                <x-form-field name="host_name" label="Nama tuan rumah / penganjur" :value="$invitation->host_name" required maxlength="255" />
                <div class="sm:col-span-2"><x-form-field name="celebrant_name" label="Nama pasangan / individu yang diraikan" :value="$invitation->celebrant_name" required maxlength="255" placeholder="Arif & Najihah" /></div>
            </div>
        </section>
        <section class="panel">
            <h2 class="text-xl font-bold mb-6">02 &nbsp; Tarikh & Lokasi</h2>
            <div class="grid sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2"><x-form-field name="event_date" label="Tarikh majlis" type="date" :value="$invitation->event_date?->format('Y-m-d')" :min="today()->format('Y-m-d')" required /></div>
                <x-form-field name="start_time" label="Masa mula" type="time" :value="substr($invitation->start_time ?? '', 0, 5)" required />
                <x-form-field name="end_time" label="Masa tamat (pilihan)" type="time" :value="substr($invitation->end_time ?? '', 0, 5)" hint="Masa tamat pada hari yang sama." />
                <div class="sm:col-span-2"><x-form-field name="venue" label="Nama lokasi / dewan" :value="$invitation->venue" required maxlength="255" /></div>
                <div class="sm:col-span-2">
                    <label for="address" class="block text-sm font-semibold mb-2">Alamat penuh <span class="text-[#92743e]">*</span></label>
                    <textarea id="address" name="address" class="form-input" rows="3" maxlength="2000" required>{{ old('address', $invitation->address) }}</textarea>
                </div>
                <div class="sm:col-span-2"><x-form-field name="map_url" label="Pautan Google Maps / Waze (pilihan)" type="url" :value="$invitation->map_url" maxlength="2048" placeholder="https://maps.google.com/..." /></div>
            </div>
        </section>
        <section class="panel">
            <h2 class="text-xl font-bold mb-6">03 &nbsp; Hubungan & Ucapan</h2>
            <div class="grid sm:grid-cols-2 gap-5">
                <x-form-field name="contact_name" label="Nama untuk dihubungi" :value="$invitation->contact_name ?? auth()->user()->name" required maxlength="255" />
                <x-form-field name="contact_phone" label="Telefon untuk dihubungi" type="tel" :value="$invitation->contact_phone ?? auth()->user()->phone" required maxlength="25" />
                <div class="sm:col-span-2">
                    <label for="message" class="block text-sm font-semibold mb-2">Ucapan jemputan (pilihan)</label>
                    <textarea id="message" name="message" rows="4" class="form-input" maxlength="3000" placeholder="Dengan segala hormatnya, kami menjemput anda...">{{ old('message', $invitation->message) }}</textarea>
                </div>
            </div>
        </section>
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-sm text-slate-500">Maklumat disimpan apabila anda klik Seterusnya.</p>
            <button class="btn-primary w-full sm:w-auto" type="submit">{{ $invitation->status === 'demo_complete' ? 'Simpan Perubahan' : 'Simpan & Seterusnya →' }}</button>
        </div>
    </form>
</div>
@endsection

