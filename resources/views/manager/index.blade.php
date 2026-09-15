@extends('layouts.manager')
@section('title', 'Portal Manager')
@section('heading', 'Ringkasan Sistem')
@section('manager-content')
<div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
@foreach ([['Pelanggan',$customers,'users','bg-blue-50 text-blue-600'], ['Manager aktif',$managers,'shield','bg-emerald-50 text-emerald-600'], ['Jumlah order',$orders,'orders','bg-violet-50 text-violet-600'], ['Menunggu bayaran',$pending,'clock','bg-amber-50 text-amber-600']] as [$label, $count, $icon, $color])
<div class="panel"><span class="inline-flex rounded-2xl p-3 mb-5 {{ $color }}"><x-icon :name="$icon" class="w-6 h-6"/></span><p class="text-sm text-slate-500">{{ $label }}</p><p class="font-serif text-4xl mt-3">{{ $count }}</p></div>
@endforeach
</div>
<div class="grid lg:grid-cols-2 gap-6 mb-8">
<section class="panel"><h2 class="font-bold text-lg mb-4 flex items-center gap-3"><x-icon name="lock" class="text-[#B6975A]"/>Kawalan sedang aktif</h2>
@forelse($blocked as $control)<div class="rounded-xl bg-amber-50 p-4 mb-3 text-sm text-amber-900"><strong>{{ \App\Models\AccessControl::LABELS[$control->key] }}</strong><p class="mt-2 break-words">{{ $control->reason }}</p></div>
@empty<p class="text-sm text-slate-500">Semua fungsi dibuka kepada pelanggan.</p>@endforelse
<a href="{{ route('manager.controls') }}" class="btn-secondary mt-5">Urus Kawalan Akses →</a></section>
<section class="panel"><h2 class="font-bold text-lg mb-4 flex items-center gap-3"><x-icon name="wallet" class="text-[#B6975A]"/>Nilai tempahan direkodkan</h2><p class="text-4xl font-serif">RM{{ number_format($simulated / 100, 2) }}</p><p class="text-sm text-slate-500 mt-4">Jumlah nilai tempahan yang direkodkan. Tiada bayaran diterima melalui sistem.</p><a href="{{ route('manager.orders') }}" class="btn-secondary mt-5">Lihat Order →</a></section>
</div>
<section class="panel"><h2 class="font-bold text-lg mb-5 flex items-center gap-3"><x-icon name="orders" class="text-[#B6975A]"/>Order Terkini</h2>
@forelse($recentOrders as $order)
<a href="{{ route('manager.orders.show', $order->id) }}" class="flex flex-wrap justify-between gap-3 py-4 border-b border-slate-100"><span class="min-w-0 break-words"><strong>{{ $order->title }}</strong><span class="block text-sm text-slate-500 mt-1">{{ $order->user?->name }} · #KT{{ $order->id }}</span></span><span class="text-sm text-[#92743e]">{{ $order->statusLabel() }} →</span></a>
@empty<p class="text-sm text-slate-500">Belum ada order.</p>@endforelse
</section>
@endsection


