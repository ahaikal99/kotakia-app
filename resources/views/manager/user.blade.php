@extends('layouts.manager')
@section('title', 'Butiran Pengguna')
@section('heading', 'Butiran Pengguna')
@section('manager-content')
<div class="grid lg:grid-cols-2 gap-6">
<section class="panel"><h2 class="font-serif text-2xl mb-5 break-words">{{ $account->name }}</h2>
<dl class="order-details">@foreach(['ID'=>$account->id,'Emel'=>$account->email,'Telefon'=>$account->phone,'Role'=>$account->isManager()?'Manager':'Pelanggan','Status'=>$account->is_active?'Aktif':'Disekat','Sebab sekatan'=>$account->blocked_reason,'Tarikh daftar'=>$account->created_at->timezone('Asia/Kuala_Lumpur')->format('d/m/Y H:i').' MYT'] as $label=>$value)<div><dt>{{ $label }}</dt><dd>{{ $value ?: '—' }}</dd></div>@endforeach</dl>
<a href="{{ route('manager.logs',['user_id'=>$account->id]) }}" class="btn-secondary mt-6">Lihat Aktiviti Pengguna →</a>
</section>
<section class="panel"><h2 class="font-bold text-xl mb-4">Kawalan Akaun</h2>
@if($account->id === auth()->id())<p class="text-sm text-slate-500">Akaun ini milik anda. Akaun sendiri tidak boleh disekat.</p>
@else
<p class="text-sm text-slate-500 mb-6">Sekatan menghalang login dan akses sesi sedia ada pada permintaan seterusnya. Data order dan log dikekalkan.</p>
<form method="POST" action="{{ route('manager.users.update',$account->id) }}" class="space-y-5">@csrf @method('PATCH')
<input type="hidden" name="is_active" value="{{ $account->is_active ? '0' : '1' }}">
@if($account->is_active)<x-form-field name="blocked_reason" label="Sebab sekatan" required maxlength="500" placeholder="Contoh: semakan akaun diperlukan"/><button class="btn-primary"><x-icon name="lock"/>Sekat Akaun</button>
@else<button class="btn-primary"><x-icon name="check"/>Aktifkan Semula Akaun</button>@endif
</form>@endif
</section></div>
<section class="panel mt-6"><h2 class="text-lg font-bold mb-4">Order Pengguna</h2>
@forelse($orders as $order)<a href="{{ route('manager.orders.show',$order->id) }}" class="flex flex-wrap justify-between gap-3 border-b border-slate-100 py-4 text-sm"><span>#KT{{ $order->id }} · {{ $order->title }}</span><span class="text-[#92743e]">{{ $order->statusLabel() }} →</span></a>@empty<p class="text-slate-500 text-sm">Tiada order.</p>@endforelse
<div class="mt-6">{{ $orders->links() }}</div></section>
@endsection


