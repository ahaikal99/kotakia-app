@extends('layouts.customer')
@section('title', 'Profil Saya')
@section('content')
<div class="max-w-3xl mx-auto">
    <a href="{{ $user->isManager() ? route('manager.index') : route('dashboard') }}" class="text-sm text-slate-500 hover:text-[#92743e]">← Kembali</a>
    <div class="mt-6 mb-8">
        <span class="eyebrow">Akaun Saya</span>
        <h1 class="font-serif text-3xl md:text-4xl mt-4">Kemaskini profil anda.</h1>
        <p class="text-slate-500 mt-3">Nama, emel dan kata laluan boleh dikemas kini di sini.</p>
    </div>
    <form method="POST" action="{{ route('profile.update') }}" class="panel space-y-6">
        @csrf @method('PUT')
        <x-form-field name="name" label="Nama" :value="$user->name" required maxlength="255" />
        <x-form-field name="email" label="Emel" type="email" :value="$user->email" required maxlength="255" />
        <div>
            <label class="block text-sm font-semibold mb-2">Nombor telefon</label>
            <div class="form-input bg-slate-100 text-slate-500" aria-label="Nombor telefon tidak boleh diedit">{{ $user->phone }}</div>
            <p class="text-xs text-slate-500 mt-2">Nombor telefon tidak boleh diubah.</p>
        </div>
        <div class="border-t border-slate-200 pt-6">
            <h2 class="font-bold text-lg mb-2">Tukar kata laluan</h2>
            <p class="text-sm text-slate-500 mb-5">Biarkan kosong jika anda mahu kekalkan kata laluan semasa.</p>
            <div class="space-y-5">
                <x-form-field name="current_password" label="Kata laluan semasa" type="password" autocomplete="current-password" />
                <x-form-field name="password" label="Kata laluan baharu" type="password" autocomplete="new-password" />
                <x-form-field name="password_confirmation" label="Sahkan kata laluan baharu" type="password" autocomplete="new-password" />
            </div>
        </div>
        <button type="submit" class="btn-primary gap-2"><x-icon name="save" />Simpan Profil</button>
    </form>
</div>
@endsection
