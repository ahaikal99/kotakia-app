@extends('layouts.manager')
@section('title', 'Kawalan Akses')
@section('heading', 'Kawalan Akses & Maintenance')
@section('manager-content')
<div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-900 mb-6">Sekatan berkuat kuasa pada permintaan seterusnya. Akses manager kekal tersedia untuk membuka semula sistem. Setiap perubahan direkodkan dalam audit trail.</div>
<div class="grid lg:grid-cols-2 gap-6">
@foreach($controls as $control)
<section class="panel">
<div class="flex flex-wrap justify-between items-start gap-3 mb-4"><h2 class="font-bold text-lg">{{ \App\Models\AccessControl::LABELS[$control->key] }}</h2><span class="rounded-full px-3 py-1 text-xs font-bold {{ $control->is_blocked ? 'bg-red-50 text-red-700' : 'bg-emerald-50 text-emerald-700' }}">{{ $control->is_blocked ? 'Disekat' : 'Dibuka' }}</span></div>
<p class="text-sm text-slate-500 mb-6">{{ ['login'=>'Sekat login pelanggan dan akses sesi pelanggan sedia ada. Login manager kekal dibuka.','registration'=>'Sekat halaman daftar dan penghantaran borang pendaftaran pelanggan.','orders'=>'Sekat halaman serta penghantaran borang cipta, edit dan pilihan pakej/design. Dashboard masih boleh dilihat.','payments'=>'Sekat halaman bayaran dan semua penghantaran bayaran, termasuk borang yang telah dibuka.'][$control->key] }}</p>
<form method="POST" action="{{ route('manager.controls.update',$control->key) }}" class="space-y-4">@csrf @method('PUT')
<input type="hidden" name="version" value="{{ hash('sha256', $control->getRawOriginal('updated_at').json_encode([$control->is_blocked, $control->reason, $control->updated_by])) }}">
<div><label for="blocked-{{ $control->key }}" class="block text-sm font-semibold mb-2">Tetapan akses</label><select id="blocked-{{ $control->key }}" name="is_blocked" class="form-input"><option value="0" @selected(!$control->is_blocked)>Buka akses</option><option value="1" @selected($control->is_blocked)>Sekat akses</option></select></div>
<div><label for="reason-{{ $control->key }}" class="block text-sm font-semibold mb-2">Sebab / mesej kepada pelanggan</label><textarea name="reason" id="reason-{{ $control->key }}" class="form-input" maxlength="500" rows="3" placeholder="Contoh: Penyelenggaraan sistem sedang dijalankan. Sila cuba semula pada jam 3 petang.">{{ $control->reason }}</textarea><p class="text-xs text-slate-500 mt-2">Wajib apabila akses disekat. Mesej ini dipaparkan kepada pelanggan.</p></div>
<button class="btn-primary"><x-icon name="save"/>Simpan {{ \App\Models\AccessControl::LABELS[$control->key] }}</button>
</form>
<p class="text-xs text-slate-500 mt-5">Kemaskini: {{ $control->updated_at->timezone('Asia/Kuala_Lumpur')->format('d/m/Y H:i:s') }} MYT @if($control->updater) · {{ $control->updater->name }} @endif</p>
</section>
@endforeach
</div>
@endsection


