@extends('layouts.manager')
@section('title', 'Tambah Design')
@section('heading', 'Tambah Design')
@section('manager-content')
<section class="panel max-w-2xl">
    <p class="text-sm text-slate-500 mb-6">Daftarkan design di sini. Fail PHP, JS dan CSS diurus secara berasingan oleh programmer.</p>
    <form method="POST" action="{{ route('manager.designs.store') }}" class="space-y-5">@csrf
        <x-form-field name="name" label="Nama design" required maxlength="255" placeholder="Contoh: Seri Melati" />
        <x-form-field name="code" label="Kod design" required maxlength="30" placeholder="Contoh: MD007" hint="Kod unik. Gambar katalog dibaca daripada public/image/KATALOG/KOD.png, contohnya MD007.png." />
        <div><label for="theme" class="block text-sm font-semibold mb-2">Kategori *</label><select id="theme" name="theme" class="form-input" required><option value="">Pilih kategori</option>@foreach($themes as $key => $label)<option value="{{ $key }}" @selected(old('theme') === $key)>{{ $label }}</option>@endforeach</select></div>
        <div><label for="is_active" class="block text-sm font-semibold mb-2">Status</label><select id="is_active" name="is_active" class="form-input"><option value="0" @selected(old('is_active', '0') == '0')>Tidak aktif — belum ditawarkan</option><option value="1" @selected(old('is_active') == '1')>Aktif — paparkan kepada pelanggan</option></select></div>
        <div class="flex flex-wrap gap-3"><button class="btn-primary" type="submit">Tambah Design</button><a class="btn-secondary" href="{{ route('manager.designs') }}">Kembali</a></div>
    </form>
</section>
@endsection
