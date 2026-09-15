@extends('layouts.customer')
@section('title', 'Dashboard')
@section('content')
@include('partials.promotion-popup')
<div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-9">
    <div>
        <span class="eyebrow">Dashboard pelanggan</span>
        <h1 class="font-serif text-3xl md:text-4xl mt-4 break-words">Selamat datang, <span class="text-[#B6975A]">{{ auth()->user()->name }}.</span></h1>
        <p class="text-slate-500 mt-3">Setiap cerita indah bermula dengan sebuah jemputan.</p>
    </div>
    <a href="{{ route('invitations.create') }}" class="btn-primary shrink-0">+ Cipta Kad</a>
</div>
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-10">
    @foreach (['draft' => 'Kad dalam draf', 'awaiting_payment' => 'Menunggu bayaran', 'demo_complete' => 'Tempahan direkodkan'] as $key => $label)
        <div class="rounded-2xl border border-slate-200 bg-white p-5 flex items-center justify-between gap-4">
            <span class="text-sm text-slate-500">{{ $label }}</span><strong class="text-3xl font-serif">{{ $counts[$key] ?? 0 }}</strong>
        </div>
    @endforeach
</div>
<h2 class="text-xl font-bold mb-5">Tempahan Saya</h2>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse ($invitations as $invitation)
        <article class="panel flex flex-col">
            <div class="flex items-start justify-between gap-3 mb-5">
                <span class="text-xs font-semibold text-slate-400">#KT{{ str_pad($invitation->id, 5, '0', STR_PAD_LEFT) }}</span>
                <span class="rounded-full bg-[#B6975A]/10 text-[#92743e] px-3 py-1 text-xs font-semibold">{{ $invitation->statusLabel() }}</span>
            </div>
            <h3 class="font-serif text-2xl mb-2 break-words">{{ $invitation->title }}</h3>
            <p class="text-sm text-slate-500">{{ config('catalog.themes.'.$invitation->theme) }} · {{ $invitation->event_date->format('d/m/Y') }}</p>
            <p class="text-sm text-slate-500 mt-4 mb-6">{{ $invitation->package_name ?? 'Pakej belum dipilih' }} @if($invitation->design_code) · {{ $invitation->design_code }} @endif</p>
            <div class="mt-auto flex flex-wrap items-center gap-3">
                <a class="btn-secondary" href="{{ $invitation->status === 'draft' ? route('invitations.options', $invitation) : route('payments.show', $invitation) }}">{{ $invitation->status === 'demo_complete' ? 'Lihat Ringkasan' : 'Sambung Tempahan' }} →</a>
                @if ($invitation->status !== 'demo_complete')<a href="{{ route('invitations.edit', $invitation) }}" class="text-sm text-slate-500 underline">Edit maklumat</a>@endif
            </div>
        </article>
    @empty
        <div class="col-span-full panel py-16 text-center">
            <div class="text-4xl text-[#B6975A] mb-5" aria-hidden="true">✉</div>
            <h3 class="font-serif text-3xl mb-3">Jemputan pertama anda menanti.</h3>
            <p class="text-slate-500 mb-7">Belum ada tempahan. Mulakan dengan maklumat majlis anda.</p>
            <a href="{{ route('invitations.create') }}" class="btn-primary">Cipta Kad Pertama</a>
        </div>
    @endforelse
</div>
<div class="mt-8">{{ $invitations->links() }}</div>
@endsection

