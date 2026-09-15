@extends('layouts.customer')
@section('title', 'Lupa Kata Laluan')
@section('content')
<div class="max-w-md mx-auto py-4 md:py-10">
    <div class="text-center mb-8"><span class="eyebrow">Akaun Kotakia</span><h1 class="font-serif text-4xl mt-5">Lupa kata laluan?</h1><p class="text-slate-500 mt-4">Masukkan emel akaun anda untuk menerima pautan reset kata laluan.</p></div>
    <section class="panel"><form method="POST" action="{{ route('password.email') }}" class="space-y-5">@csrf
        <x-form-field name="email" label="Emel" type="email" required autocomplete="email" maxlength="255" />
        <button class="btn-primary w-full" type="submit">Hantar pautan reset</button>
    </form><p class="mt-6 text-center text-sm"><a class="text-[#92743e] underline" href="{{ route('login') }}">Kembali ke log masuk</a></p></section>
</div>
@endsection
