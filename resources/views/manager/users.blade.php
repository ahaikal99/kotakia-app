@extends('layouts.manager')
@section('title', 'Pengguna & Manager')
@section('heading', 'Pengguna & Manager')
@section('manager-content')
<div class="flex justify-end mb-6"><a class="btn-primary" href="{{ route('manager.users.create') }}"><x-icon name="plus"/>Tambah Manager</a></div>
<form class="panel grid md:grid-cols-4 gap-4 items-end mb-6" method="GET">
<x-form-field name="q" label="Cari pengguna" :value="request('q')" placeholder="Nama, emel atau telefon" maxlength="100"/>
<div><label for="role" class="block text-sm font-semibold mb-2">Role</label><select id="role" name="role" class="form-input"><option value="">Semua role</option><option value="customer" @selected(request('role')==='customer')>Pelanggan</option><option value="manager" @selected(request('role')==='manager')>Manager</option></select></div>
<div><label for="active" class="block text-sm font-semibold mb-2">Status akaun</label><select id="active" name="active" class="form-input"><option value="">Semua status</option><option value="1" @selected(request('active')==='1')>Aktif</option><option value="0" @selected(request('active')==='0')>Disekat</option></select></div>
<div class="flex gap-4 items-center"><button class="btn-primary"><x-icon name="search"/>Cari</button><a href="{{ route('manager.users') }}" class="text-sm underline">Reset</a></div>
</form>
<p class="text-sm text-slate-500 mb-4">{{ $users->total() }} pengguna ditemui</p>
<div class="panel overflow-x-auto"><table class="manager-table"><thead><tr><th>Nama / Emel</th><th>Telefon</th><th>Role</th><th>Status</th><th>Order</th><th>Butiran</th></tr></thead><tbody>
@forelse($users as $account)<tr><td><strong>{{ $account->name }}</strong><span class="block mt-1 text-slate-500">{{ $account->email }}</span></td><td>{{ $account->phone ?? '—' }}</td><td>{{ $account->isManager() ? 'Manager' : 'Pelanggan' }}</td><td><span class="{{ $account->is_active ? 'text-emerald-700' : 'text-red-700' }}">{{ $account->is_active ? 'Aktif' : 'Disekat' }}</span></td><td>{{ $account->invitations_count }}</td><td><a href="{{ route('manager.users.show',$account->id) }}" class="underline text-[#92743e]">Lihat</a></td></tr>
@empty<tr><td colspan="6">Tiada pengguna sepadan dengan carian.</td></tr>@endforelse
</tbody></table></div><div class="mt-6">{{ $users->links() }}</div>
@endsection


