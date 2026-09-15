@extends('layouts.customer')
@section('title', 'Daftar Akaun')
@section('content')
<div class="max-w-5xl mx-auto grid lg:grid-cols-2 gap-10 lg:gap-16 items-start">
    <div class="lg:pt-14">
        <span class="eyebrow">Bermula dengan satu jemputan</span>
        <h1 class="font-serif text-4xl md:text-5xl leading-tight mt-5">Momen istimewa,<br><span class="text-[#B6975A] italic">bermula di sini.</span></h1>
        <p class="text-slate-500 mt-6 leading-relaxed max-w-sm">Daftar akaun untuk mencipta kad jemputan dan mengurus semua tempahan anda dalam satu tempat.</p>
        <ol class="mt-8 space-y-4 text-sm text-slate-600">
            <li>01 &nbsp; Cipta akaun anda</li>
            <li>02 &nbsp; Lengkapkan maklumat majlis</li>
            <li>03 &nbsp; Pilih pakej dan reka bentuk kegemaran</li>
        </ol>
    </div>
    <section class="panel">
        <h2 class="text-2xl font-bold mb-2">Daftar Akaun</h2>
        <p class="text-sm text-slate-500 mb-7">Sudah mempunyai akaun? <a href="{{ route('login') }}" class="text-[#92743e] font-semibold underline">Log masuk</a></p>
        <form method="POST" action="{{ route('register.store') }}" class="space-y-5">
            @csrf
            <x-form-field name="name" label="Nama penuh" required autocomplete="name" maxlength="255" />
            <x-form-field name="phone" label="Nombor telefon" type="tel" required autocomplete="tel" maxlength="25" placeholder="0123456789" hint="Nombor Malaysia atau antarabangsa dengan kod negara." />
            <x-form-field name="email" label="Emel" type="email" required autocomplete="email" maxlength="255" />
            <x-form-field name="password" label="Kata laluan" type="password" required autocomplete="new-password" minlength="8" hint="Sekurang-kurangnya 8 aksara." />
            <x-form-field name="password_confirmation" label="Sahkan kata laluan" type="password" required autocomplete="new-password" minlength="8" />
            <button type="submit" class="btn-primary w-full">Daftar Akaun →</button>
        </form>
    </section>
</div>
@endsection

