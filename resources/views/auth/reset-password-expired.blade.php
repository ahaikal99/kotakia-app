@extends('layouts.customer')
@section('title', 'Pautan Reset Tidak Sah')
@section('content')
<div class="max-w-md mx-auto py-4 md:py-10">
    <section class="panel text-center">
        <span class="eyebrow">Keselamatan akaun</span>
        <h1 class="font-serif text-3xl mt-5">Pautan tidak lagi sah.</h1>
        <p class="text-slate-500 mt-4 mb-6">Pautan ini telah tamat tempoh, sudah digunakan atau tidak sah. Sila mohon pautan baharu untuk menetapkan semula kata laluan anda.</p>
        <a class="btn-primary w-full" href="{{ route('password.request') }}">Mohon pautan baharu</a>
    </section>
</div>
@endsection
