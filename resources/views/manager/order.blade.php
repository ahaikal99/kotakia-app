@extends('layouts.manager')
@section('title', 'Butiran Order')
@section('heading', 'Butiran Order #KT'.$order->id)
@section('manager-content')
<div class="grid lg:grid-cols-3 gap-6">
<section class="panel lg:col-span-2"><h2 class="font-serif text-2xl mb-5 break-words">{{ $order->title }}</h2>
<dl class="order-details">
@foreach(['Pelanggan'=>$order->user?->name, 'Emel'=>$order->user?->email, 'Status'=>$order->statusLabel(), 'Tema'=>config('catalog.themes.'.$order->theme), 'Tuan rumah'=>$order->host_name, 'Nama yang diraikan'=>$order->celebrant_name, 'Tarikh'=>$order->event_date->format('d/m/Y'), 'Masa'=>substr($order->start_time,0,5).' – '.substr($order->end_time ?? '',0,5), 'Lokasi'=>$order->venue, 'Alamat'=>$order->address, 'Pautan peta'=>$order->map_url, 'Hubungan'=>$order->contact_name.' · '.$order->contact_phone, 'Ucapan'=>$order->message] as $label=>$value)
<div><dt>{{ $label }}</dt><dd class="whitespace-pre-line">{{ $value ?: '—' }}</dd></div>@endforeach
</dl>
@if($order->user)<a href="{{ route('manager.users.show',$order->user_id) }}" class="btn-secondary mt-6">Lihat Pelanggan</a>@endif
</section>
<section class="panel"><h2 class="text-lg font-bold mb-5">Pakej & Bayaran</h2>
@if($order->design_code)<img src="{{ asset('image/KATALOG/'.$order->design_code.'.png') }}" alt="{{ $order->design_name }}" class="h-52 w-full object-contain bg-slate-50 rounded-xl mb-5">@endif
<p class="font-semibold">{{ $order->package_name ?? 'Pakej belum dipilih' }}</p><p class="text-sm text-slate-500 mt-2">{{ $order->design_name }} {{ $order->design_code }}</p>
<p class="font-serif text-3xl my-6">{{ $order->amount_cents !== null ? 'RM'.number_format($order->amount_cents / 100,2) : '—' }}</p>
@if($order->payment)<div class="rounded-xl bg-emerald-50 p-4 text-sm text-emerald-900"><strong>Tempahan direkodkan</strong><p class="break-all mt-2">{{ $order->payment->reference }}</p><p class="mt-2">{{ $order->payment->simulated_at->timezone('Asia/Kuala_Lumpur')->format('d/m/Y H:i') }} MYT</p></div>@else<p class="text-sm text-slate-500">Tiada rekod bayaran.</p>@endif
<p class="text-xs text-slate-500 mt-4">Bayaran dalam talian belum tersedia.</p>
</section></div>
@endsection

