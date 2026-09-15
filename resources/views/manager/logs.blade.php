@extends('layouts.manager')
@section('title', 'Log Sistem')
@section('heading', 'Audit Trail Sistem')
@section('manager-content')
<form method="GET" class="panel grid sm:grid-cols-2 lg:grid-cols-3 gap-4 items-end mb-6">
<div><label for="action" class="block text-sm font-semibold mb-2">Tindakan</label><select id="action" name="action" class="form-input"><option value="">Semua tindakan</option>@foreach($actions as $action)<option value="{{ $action }}" @selected(request('action')===$action)>{{ $action }}</option>@endforeach</select></div>
<x-form-field name="ip" label="Alamat IP" :value="request('ip')" placeholder="Contoh: 172.20.0.1"/>
<x-form-field name="user_id" label="ID pengguna" type="number" min="1" :value="request('user_id')"/>
<div><label for="outcome" class="block text-sm font-semibold mb-2">Keputusan</label><select id="outcome" name="outcome" class="form-input"><option value="">Semua keputusan</option>@foreach(['success'=>'Berjaya','failed'=>'Gagal / Disekat','redirected'=>'Dialihkan'] as $value=>$label)<option value="{{ $value }}" @selected(request('outcome')===$value)>{{ $label }}</option>@endforeach</select></div>
<x-form-field name="from" label="Dari tarikh (MYT)" type="date" :value="request('from')"/>
<x-form-field name="to" label="Hingga tarikh (MYT)" type="date" :value="request('to')"/>
<div class="flex items-center gap-4"><button class="btn-primary"><x-icon name="search"/>Tapis Log</button><a href="{{ route('manager.logs') }}" class="text-sm underline">Reset</a></div>
</form>
<p class="text-sm text-slate-500 mb-4">{{ $logs->total() }} rekod ditemui · Masa Malaysia (MYT)</p>
<div class="panel overflow-x-auto"><table class="manager-table"><thead><tr><th>Masa</th><th>Pengguna</th><th>Tindakan</th><th>IP</th><th>Keputusan</th><th>Butiran</th></tr></thead><tbody>
@forelse($logs as $log)<tr><td class="whitespace-nowrap">{{ $log->created_at->timezone('Asia/Kuala_Lumpur')->format('d/m/Y H:i:s') }}</td><td>{{ $log->user?->name ?? ($log->user_id ? 'Pengguna #'.$log->user_id : 'Pelawat') }}</td><td>{{ $log->action }}</td><td>{{ $log->ip_address ?? 'CLI / Tiada IP' }}</td><td>{{ ['success'=>'Berjaya','failed'=>'Gagal','redirected'=>'Dialihkan'][$log->outcome] ?? $log->outcome }} · {{ $log->status_code }}</td><td><a href="{{ route('manager.logs.show',$log->id) }}" class="underline text-[#92743e]">Lihat</a></td></tr>
@empty<tr><td colspan="6">Tiada log sepadan dengan penapis.</td></tr>@endforelse
</tbody></table></div><div class="mt-6">{{ $logs->links() }}</div>
@endsection


