@extends('layouts.customer')
@section('title', 'Tetapkan Kata Laluan Baharu')
@section('content')
<div class="max-w-md mx-auto py-4 md:py-10">
    <div class="text-center mb-8"><span class="eyebrow">Akaun Kotakia</span><h1 class="font-serif text-4xl mt-5">Kata laluan baharu.</h1><p class="text-slate-500 mt-4">Gunakan sekurang-kurangnya 8 aksara.</p></div>
    <section class="panel"><form method="POST" action="{{ route('password.update') }}" class="space-y-5">@csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <x-form-field name="email" label="Emel" type="email" :value="is_string($email) ? $email : ''" required autocomplete="email" maxlength="255" />
        <x-form-field name="password" label="Kata laluan baharu" type="password" required autocomplete="new-password" minlength="8" />
        <x-form-field name="password_confirmation" label="Sahkan kata laluan" type="password" required autocomplete="new-password" minlength="8" />
        <button class="btn-primary w-full" type="submit">Simpan kata laluan</button>
    </form><p class="mt-6 text-center text-sm"><a class="text-[#92743e] underline" href="{{ route('password.request') }}">Mohon pautan baharu</a></p></section>
</div>
@endsection
