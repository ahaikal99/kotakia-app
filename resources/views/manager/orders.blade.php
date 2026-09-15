@extends('layouts.manager')
@section('title', 'Order')
@section('heading', 'Semua Order')
@section('manager-content')
<form method="GET" class="panel grid sm:grid-cols-3 gap-4 mb-6 items-end">
<x-form-field name="q" label="Cari order" :value="request('q')" placeholder="Tajuk, ID order, nama atau emel" maxlength="100"/>
<div><label for="status" class="block text-sm font-semibold mb-2">Status</label><select class="form-input" id="status" name="status"><option value="">Semua status</option>@foreach(['draft'=>'Draf','awaiting_payment'=>'Menunggu bayaran','demo_complete'=>'Tempahan direkodkan'] as $value=>$label)<option value="{{ $value }}" @selected(request('status')===$value)>{{ $label }}</option>@endforeach</select></div>
<div class="flex items-center gap-4"><button class="btn-primary"><x-icon name="search"/>Cari</button><a href="{{ route('manager.orders') }}" class="text-sm underline">Reset</a></div>
</form>
<p class="text-sm text-slate-500 mb-4">{{ $orders->total() }} order ditemui</p>
<div class="panel overflow-x-auto"><table class="manager-table"><thead><tr><th>Order / Pelanggan</th><th>Majlis</th><th>Pakej & Design</th><th>Jumlah</th><th>Status</th><th>Butiran</th></tr></thead><tbody>
@forelse($orders as $order)<tr><td><strong>#KT{{ $order->id }}</strong><span class="block mt-1">{{ $order->user?->name }}</span></td><td>{{ $order->title }}<span class="block text-slate-500 mt-1">{{ $order->event_date->format('d/m/Y') }}</span></td><td>{{ $order->package_name ?? 'Belum dipilih' }}<span class="block text-slate-500 mt-1">{{ $order->design_code }}</span></td><td>{{ $order->amount_cents !== null ? 'RM'.number_format($order->amount_cents/100,2) : '—' }}</td><td>{{ $order->statusLabel() }}</td><td><a class="text-[#92743e] underline" href="{{ route('manager.orders.show', $order->id) }}">Lihat</a></td></tr>
@empty<tr><td colspan="6">Tiada order sepadan dengan carian.</td></tr>@endforelse
</tbody></table></div><div class="mt-6">{{ $orders->links() }}</div>
@endsection


