@extends('layouts.customer')
@section('title', 'Pilih Pakej & Reka Bentuk')
@section('content')
<div class="max-w-5xl mx-auto">
    <a href="{{ $invitation->status === 'demo_complete' ? route('payments.show', $invitation) : route('invitations.edit', $invitation) }}" class="text-sm text-slate-500">← {{ $invitation->status === 'demo_complete' ? 'Kembali ke ringkasan' : 'Edit maklumat kad' }}</a>
    <h1 class="font-serif text-3xl md:text-4xl mt-6">Sentuhan indah untuk <span class="text-[#B6975A] italic">jemputan anda.</span></h1>
    <p class="text-slate-500 mt-3 mb-8 break-words">{{ $invitation->title }} · {{ $invitation->event_date->format('d/m/Y') }}</p>
    <x-order-steps :step="2" />
    <form method="POST" action="{{ route('invitations.options.save', $invitation) }}" class="space-y-9">
        @csrf @method('PUT')
        @if($invitation->status === 'demo_complete')
        <section class="panel">
            <span class="eyebrow">Pakej dikunci</span>
            <div class="flex flex-wrap justify-between gap-3 mt-3"><strong>{{ $invitation->package_name }}</strong><span>RM{{ number_format($invitation->amount_cents / 100, 2) }}</span></div>
            <p class="text-sm text-slate-500 mt-3">Pakej tidak boleh diubah selepas tempahan direkodkan.</p>
        </section>
        @else
        <fieldset>
            <legend class="text-xl font-bold mb-5">Pilih Pakej</legend>
            <div class="grid md:grid-cols-3 gap-5">
                @foreach ($packages as $code => $package)
                    <label class="choice-card p-6">
                        <span class="flex items-center justify-between gap-3"><strong>{{ $package['name'] }}</strong><input type="radio" name="package_code" value="{{ $code }}" required @checked(old('package_code', $invitation->package_code) === $code)></span>
                        <span class="block font-bold text-3xl mt-5">RM{{ number_format($package['amount_cents'] / 100, 0) }} <span class="text-xs font-normal text-slate-500">/kad</span></span>
                        <span class="block text-sm text-slate-500 mt-3 mb-5">{{ $package['description'] }}</span>
                        <span class="block space-y-3 text-sm text-slate-600">@foreach ($package['features'] as $feature)<span class="block">✓ {{ $feature }}</span>@endforeach</span>
                    </label>
                @endforeach
            </div>
        </fieldset>
        @endif
        <fieldset>
            <legend class="text-xl font-bold mb-2">Pilih Reka Bentuk</legend>
            <p class="text-slate-500 text-sm mb-5">Tema {{ config('catalog.themes.'.$invitation->theme) }} · {{ $designs->count() }} reka bentuk tersedia</p>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse ($designs as $design)
                    <label class="choice-card p-3">
                        <span class="block rounded-2xl bg-[#f1eee8] p-4 h-64 mb-4"><img class="w-full h-full object-contain" src="{{ asset('image/KATALOG/'.$design['code'].'.png') }}" alt="{{ $design['name'] }}" loading="lazy"></span>
                        <span class="flex items-center justify-between gap-3 px-2 pb-3"><span><strong class="block font-serif text-xl">{{ $design['name'] }}</strong><span class="text-xs text-slate-500">{{ $design['code'] }}</span></span><input type="radio" name="design_code" value="{{ $design['code'] }}" required @checked(old('design_code', $invitation->design_code) === $design['code'])></span>
                    </label>
                @empty
                    <div class="col-span-full panel text-center py-10">
                        <h2 class="font-serif text-2xl mb-3">Reka bentuk tema ini belum tersedia.</h2>
                        <p class="text-slate-500 mb-5">Draf anda telah disimpan. Anda boleh menukar tema atau sambung kemudian.</p>
                        <a class="btn-secondary" href="{{ route('invitations.edit', $invitation) }}">Tukar Tema</a>
                    </div>
                @endforelse
            </div>
        </fieldset>
        <div class="flex flex-col sm:flex-row justify-between gap-4 items-center">
            <a class="text-sm text-slate-500 underline" href="{{ $invitation->status === 'demo_complete' ? route('payments.show', $invitation) : route('dashboard') }}">{{ $invitation->status === 'demo_complete' ? 'Batal' : 'Sambung kemudian di dashboard' }}</a>
            <button type="submit" class="btn-primary w-full sm:w-auto disabled:opacity-40 disabled:cursor-not-allowed" @disabled($designs->isEmpty())>{{ $invitation->status === 'demo_complete' ? 'Simpan Design' : 'Seterusnya: Bayaran →' }}</button>
        </div>
    </form>
</div>
@endsection

