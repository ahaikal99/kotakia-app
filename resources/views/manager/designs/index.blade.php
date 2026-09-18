@extends('layouts.manager')
@section('title', 'Design')
@section('heading', 'Koleksi Design')
@section('manager-content')
<div class="flex flex-wrap justify-between items-center gap-4 mb-6"><p class="text-sm text-slate-500">Aktifkan design yang sedia ditawarkan kepada pelanggan.</p><a href="{{ route('manager.designs.create') }}" class="btn-primary gap-2"><x-icon name="plus"/>Tambah Design</a></div>
<form method="GET" class="panel mb-6 grid sm:grid-cols-4 gap-4 items-end">
    <x-form-field name="q" label="Carian" :value="request('q')" placeholder="Nama atau kod" />
    <div><label for="theme" class="block text-sm font-semibold mb-2">Kategori</label><select id="theme" name="theme" class="form-input"><option value="">Semua kategori</option>@foreach($themes as $key => $label)<option value="{{ $key }}" @selected(request('theme') === $key)>{{ $label }}</option>@endforeach</select></div>
    <div><label for="status" class="block text-sm font-semibold mb-2">Status</label><select id="status" name="status" class="form-input"><option value="">Semua status</option><option value="active" @selected(request('status') === 'active')>Aktif</option><option value="inactive" @selected(request('status') === 'inactive')>Tidak aktif</option></select></div>
    <button class="btn-secondary" type="submit">Tapis design</button>
</form>
<div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-5">
@forelse($designs as $design)
<article class="panel !p-5">
    <div class="bg-[#f6f2e9] rounded-xl p-4 mb-5 h-48"><img src="{{ $design->thumbnailUrl() }}" alt="{{ $design->name }}" class="w-full h-full object-contain" loading="lazy"></div>
    <div class="flex justify-between items-center gap-3 text-xs mb-3"><span class="font-bold text-[#92743e]">{{ $design->code }}</span><span class="rounded-full px-3 py-1 {{ $design->is_active ? 'bg-emerald-50 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">{{ $design->is_active ? 'Aktif' : 'Tidak aktif' }}</span></div>
    <h2 class="font-serif text-xl">{{ $design->name }}</h2><p class="text-sm text-slate-500 mt-2 mb-5">{{ $themes[$design->theme] ?? $design->theme }}</p>
    <a href="{{ $design->previewUrl() }}" target="_blank" rel="noopener" class="btn-secondary w-full mb-3 gap-2"><x-icon name="eye"/>Preview</a>
    <form method="POST" action="{{ route('manager.designs.status', $design) }}">@csrf @method('PATCH')<input type="hidden" name="is_active" value="{{ $design->is_active ? '0' : '1' }}"><button type="submit" class="btn-secondary w-full gap-2"><x-icon :name="$design->is_active ? 'lock' : 'check'"/>{{ $design->is_active ? 'Nyahaktifkan' : 'Aktifkan' }}</button></form>
</article>
@empty
<div class="panel sm:col-span-2 xl:col-span-3 text-center text-slate-500">Tiada design ditemui.</div>
@endforelse
</div>
<div class="mt-6">{{ $designs->links() }}</div>
@endsection
