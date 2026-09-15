@extends('layouts.manager')
@section('title', 'Tambah Manager')
@section('heading', 'Tambah Akaun Manager')
@section('manager-content')
<section class="panel max-w-2xl mx-auto">
<p class="text-sm text-slate-500 mb-7">Akaun ini mendapat akses kepada semua log, order, pengguna dan kawalan sistem. Hanya manager boleh menambah akaun manager.</p>
<form action="{{ route('manager.users.store') }}" method="POST" class="space-y-5">@csrf
<x-form-field name="name" label="Nama penuh" required maxlength="255" autocomplete="off"/>
<x-form-field name="phone" label="Nombor telefon" type="tel" required maxlength="25" placeholder="0123456789"/>
<x-form-field name="email" label="Emel" type="email" required maxlength="255" autocomplete="off"/>
<x-form-field name="password" label="Kata laluan" type="password" required minlength="8" autocomplete="new-password" hint="Sekurang-kurangnya 8 aksara."/>
<x-form-field name="password_confirmation" label="Sahkan kata laluan" type="password" required minlength="8" autocomplete="new-password"/>
<button class="btn-primary"><x-icon name="users"/>Cipta Akaun Manager</button>
</form>
</section>
@endsection


