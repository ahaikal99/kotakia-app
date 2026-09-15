@extends('layouts.customer')
@section('title', 'Log Masuk')
@section('content')
<div class="max-w-md mx-auto py-4 md:py-10">
    <div class="text-center mb-8">
        <span class="eyebrow">Ruang pelanggan Kotakia</span>
        <h1 class="font-serif text-4xl mt-5">Selamat <span class="italic text-[#B6975A]">kembali.</span></h1>
        <p class="text-slate-500 mt-4">Log masuk untuk mengurus jemputan anda.</p>
    </div>
    <section class="panel">
        @if ($loginBlock ?? null)
            <div role="status" class="mb-5 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">{{ $loginBlock->reason }}<p class="mt-2">Akses pelanggan dihentikan sementara. Manager masih boleh log masuk.</p></div>
        @endif
        <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
            @csrf
            <x-form-field name="email" label="Emel" type="email" required autocomplete="email" maxlength="255" />
            <x-form-field name="password" label="Kata laluan" type="password" required autocomplete="current-password" />
            <div class="text-right"><a href="{{ route('password.request') }}" class="text-sm font-semibold text-[#92743e] underline">Lupa kata laluan?</a></div>
            <button type="submit" class="btn-primary w-full">Log Masuk →</button>
        </form>
        <p class="mt-6 text-center text-sm text-slate-500">Belum mempunyai akaun? <a href="{{ route('register') }}" class="font-semibold text-[#92743e] underline">Daftar sekarang</a></p>
    </section>
</div>
@endsection

