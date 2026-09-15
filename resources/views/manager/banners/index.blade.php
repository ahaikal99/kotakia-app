@extends('layouts.manager')
@section('title', 'Banner Promosi')
@section('heading', 'Banner & Popup Promosi')
@section('manager-content')
<div class="flex flex-col sm:flex-row gap-4 justify-between items-start mb-7"><p class="text-sm text-slate-500 max-w-xl leading-relaxed">Poster aktif yang paling baharu akan muncul di dashboard pelanggan. Banner tamat tempoh berhenti dipaparkan secara automatik.</p><a href="{{ route('manager.banners.create') }}" class="btn-primary shrink-0"><x-icon name="plus"/>Tambah Banner</a></div>
<div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-6">
@forelse($banners as $banner)
<article class="panel flex flex-col">
<img src="{{ route('promotions.poster',$banner->id) }}" alt="{{ $banner->title }}" class="h-56 w-full object-contain bg-slate-50 rounded-xl mb-5" loading="lazy">
<div class="flex justify-between gap-2 items-start"><h2 class="font-bold text-lg break-words">{{ $banner->title }}</h2><span class="text-xs shrink-0 rounded-full px-3 py-1 {{ $banner->statusLabel()==='Aktif' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ $banner->statusLabel() }}</span></div>
<p class="text-sm text-slate-500 mt-3 mb-6">Luput: {{ $banner->expires_at->copy()->subSecond()->timezone('Asia/Kuala_Lumpur')->format('d/m/Y H:i:s') }} MYT</p>
<div class="flex flex-wrap gap-3 mt-auto"><a class="btn-secondary" href="{{ route('manager.banners.edit',$banner->id) }}"><x-icon name="eye"/>Lihat / Edit</a>
@if($banner->is_active)<form method="POST" action="{{ route('manager.banners.deactivate',$banner->id) }}">@csrf @method('PATCH')<button class="btn-secondary text-red-700"><x-icon name="lock"/>Nyahaktifkan</button></form>@endif
</div></article>
@empty<div class="panel sm:col-span-2 xl:col-span-3 text-center py-16"><x-icon name="image" class="w-12 h-12 mx-auto text-[#B6975A] mb-5"/><h2 class="font-serif text-2xl mb-3">Promosi pertama anda menanti.</h2><p class="text-slate-500">Tambah poster dan tarikh luput untuk memaparkannya kepada pelanggan.</p></div>@endforelse
</div><div class="mt-6">{{ $banners->links() }}</div>
@endsection

