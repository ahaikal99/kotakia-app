<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>@yield('title', 'Portal Manager') — KOTAKIA.MY</title>
<link rel="icon" href="{{ asset('image/kotakia.png') }}">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&family=Playfair+Display:ital,wght@0,700;1,700&display=swap" rel="stylesheet">
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="catalog-page customer-page manager-shell bg-slate-50 text-slate-900 antialiased">
<a href="#main-content" class="sr-only focus:not-sr-only">Terus ke kandungan</a>
<details class="manager-sidebar" data-manager-menu open>
<summary class="manager-menu-toggle">
<span class="flex items-center gap-2"><img src="{{ asset('image/kotakia.png') }}" alt="" class="w-8 h-8"><strong><span class="text-[#B6975A]">KOTAKIA</span>.MY</strong></span>
<span class="flex items-center gap-2 text-sm"><x-icon name="menu"/> Menu</span>
</summary>
<div class="manager-sidebar-body">
<a href="{{ route('manager.index') }}" class="manager-brand items-center gap-3"><img src="{{ asset('image/kotakia.png') }}" alt="" class="w-10 h-10"><span class="font-bold text-xl tracking-tight"><span class="text-[#B6975A]">KOTAKIA</span>.MY</span></a>
<div class="mt-8 mb-5 flex items-center gap-2 rounded-xl border border-[#B6975A]/20 bg-[#B6975A]/10 px-3 py-2.5 text-xs font-bold text-[#92743e]"><x-icon name="shield" class="w-4 h-4"/> RUANG MANAGER</div>
<p class="text-[10px] uppercase tracking-[0.2em] text-slate-400 font-bold mb-3 px-3">Menu utama</p>
<nav aria-label="Navigasi manager" class="space-y-2">
@foreach (['manager.index' => ['Ringkasan','grid'], 'manager.orders' => ['Order','orders'], 'manager.users' => ['Pengguna & Manager','users'], 'manager.designs' => ['Design','image'], 'manager.banners' => ['Banner Promosi','image'], 'manager.logs' => ['Log Sistem','logs'], 'manager.controls' => ['Kawalan Akses','lock']] as $route => [$label, $icon])
<a href="{{ route($route) }}" class="manager-nav-link {{ request()->routeIs($route, $route.'.*') ? 'is-active' : '' }}" @if(request()->routeIs($route, $route.'.*')) aria-current="page" @endif><x-icon :name="$icon"/><span>{{ $label }}</span>@if(request()->routeIs($route, $route.'.*'))<span class="ml-auto h-1.5 w-1.5 rounded-full bg-[#B6975A]" aria-hidden="true"></span>@endif</a>
@endforeach
</nav>
<div class="mt-auto pt-8">
<p class="text-[10px] uppercase tracking-[0.2em] text-slate-400 font-bold mb-3 px-3">Pautan pantas</p>
<a href="{{ url('/') }}" class="manager-nav-link"><x-icon name="home"/> Laman Utama <x-icon name="external" class="ml-auto w-4 h-4"/></a>
<a href="{{ route('catalog') }}" class="manager-nav-link"><x-icon name="eye"/> Katalog</a>
<div class="border-t border-slate-100 mt-5 pt-5">
<div class="flex items-center gap-3 px-3 mb-4"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 font-bold text-[#92743e]">{{ mb_strtoupper(mb_substr(auth()->user()->name,0,1)) }}</span><span class="min-w-0"><strong class="block text-sm truncate">{{ auth()->user()->name }}</strong><span class="text-xs text-slate-400">Manager Kotakia</span></span></div>
<form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="manager-nav-link w-full text-red-700"><x-icon name="logout"/> Log Keluar</button></form>
</div>
</div>
</div>
</details>
<div class="manager-workspace">
<header class="manager-topbar"><span class="flex items-center gap-2 text-sm text-slate-500"><x-icon name="shield" class="text-[#B6975A]"/> Portal Manager</span><a href="{{ route('manager.users.create') }}" class="btn-secondary gap-2"><x-icon name="plus" class="w-4 h-4"/> Tambah Manager</a></header>
<main id="main-content" class="manager-content">
<div class="mb-8"><span class="eyebrow">Pentadbiran Kotakia</span><h1 class="font-serif text-3xl md:text-4xl mt-3">@yield('heading', 'Portal Manager')</h1><p class="mt-3 text-sm text-slate-500">Urus operasi, pelanggan dan akses sistem di satu tempat.</p></div>
@if(session('status'))<div role="status" class="flex items-start gap-3 mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 text-emerald-900 px-5 py-4 text-sm"><x-icon name="check"/><span>{{ session('status') }}</span></div>@endif
@if($errors->any())<div role="alert" class="mb-6 rounded-2xl border border-red-200 bg-red-50 text-red-900 px-5 py-4 text-sm"><p class="font-bold mb-2">Sila semak maklumat berikut:</p><ul class="list-disc pl-5 space-y-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@yield('manager-content')
<footer class="mt-12 pt-6 border-t border-slate-200 flex flex-wrap gap-2 justify-between text-xs text-slate-400"><span>© {{ date('Y') }} KOTAKIA.MY</span><span>Ruang pengurusan jemputan digital</span></footer>
</main>
</div>
</body>
</html>

