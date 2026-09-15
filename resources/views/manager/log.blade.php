@extends('layouts.manager')
@section('title', 'Butiran Audit')
@section('heading', 'Butiran Log #'.$log->id)
@section('manager-content')
<section class="panel"><dl class="order-details">
@foreach(['Tindakan'=>$log->action,'Keputusan'=>$log->outcome,'Pengguna'=>$log->user?->name ?? 'Pelawat / Akaun tiada','ID pengguna'=>$log->user_id,'Masa'=>$log->created_at->timezone('Asia/Kuala_Lumpur')->format('d/m/Y H:i:s').' MYT','IP'=>$log->ip_address,'Pelayar'=>$log->user_agent,'Kaedah'=>$log->method,'Laluan'=>$log->route_name,'Path'=>$log->route_path,'Status HTTP'=>$log->status_code,'Rekod terlibat'=>$log->subject_type.' #'.$log->subject_id] as $label=>$value)<div><dt>{{ $label }}</dt><dd class="break-all">{{ $value ?: '—' }}</dd></div>@endforeach
</dl></section>
<section class="panel mt-6"><h2 class="font-bold text-lg mb-4">Perubahan & Maklumat Tambahan</h2><pre class="text-xs sm:text-sm p-4 bg-slate-50 border border-slate-200 rounded-xl whitespace-pre-wrap break-all">{{ json_encode($log->metadata ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre><p class="text-xs text-slate-500 mt-4">Log ini untuk semakan sahaja. Tiada fungsi edit atau padam.</p></section>
@endsection

