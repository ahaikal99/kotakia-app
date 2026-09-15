@extends('layouts.manager')
@section('title', $banner->exists ? 'Edit Banner' : 'Tambah Banner')
@section('heading', $banner->exists ? 'Edit Banner Promosi' : 'Tambah Banner Promosi')
@section('manager-content')
<form method="POST" enctype="multipart/form-data" action="{{ $banner->exists ? route('manager.banners.update',$banner->id) : route('manager.banners.store') }}" class="grid lg:grid-cols-2 gap-6 items-start">
@csrf @if($banner->exists) @method('PUT') @endif
<section class="panel space-y-6">
<x-form-field name="title" label="Tajuk promosi" :value="$banner->title" required maxlength="150" hint="Tajuk juga digunakan sebagai penerangan poster untuk pembaca skrin."/>
<div><label for="poster" class="block text-sm font-semibold mb-2">Poster promosi {{ $banner->exists ? '(pilihan jika tidak ditukar)' : '*' }}</label><input id="poster" name="poster" type="file" accept="image/jpeg,image/png,image/webp" @required(!$banner->exists) class="form-input" data-poster-upload><p class="text-xs text-slate-500 mt-2">JPG, PNG atau WebP. Maksimum 2 MB dan 6000 × 6000 piksel.</p></div>
<x-form-field name="expires_on" label="Tarikh luput" type="date" :value="$banner->expires_at?->copy()->subSecond()->timezone('Asia/Kuala_Lumpur')->format('Y-m-d')" :min="now('Asia/Kuala_Lumpur')->format('Y-m-d')" required hint="Promosi aktif sehingga 11:59:59 malam pada tarikh ini (waktu Malaysia)."/>
<div><label for="is_active" class="block text-sm font-semibold mb-2">Status banner</label><select id="is_active" name="is_active" class="form-input"><option value="1" @selected((string)old('is_active', $banner->exists ? (int)$banner->is_active : 1)==='1')>Aktif</option><option value="0" @selected((string)old('is_active', $banner->exists ? (int)$banner->is_active : 1)==='0')>Tidak aktif / Draf</option></select></div>
<div class="flex gap-3 flex-wrap"><button class="btn-primary"><x-icon name="save"/>Simpan Banner</button><a class="btn-secondary" href="{{ route('manager.banners') }}">Kembali</a></div>
</section>
<section class="panel"><h2 class="text-lg font-bold mb-5 flex items-center gap-2"><x-icon name="image" class="text-[#B6975A]"/>Pratonton Poster</h2>
<img data-poster-preview @if($banner->exists) src="{{ route('promotions.poster',$banner->id) }}" @else hidden @endif alt="Pratonton poster promosi" class="w-full max-h-[520px] object-contain rounded-xl bg-slate-50">
<p class="text-sm text-slate-500 leading-relaxed mt-5">Popup muncul setiap kali pelanggan memuatkan dashboard, selagi banner aktif dan belum tamat tempoh. Pelanggan boleh menutup popup melalui butang tutup.</p>
</section>
</form>
@endsection

