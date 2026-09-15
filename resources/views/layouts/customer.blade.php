<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Akaun Saya') — KOTAKIA.MY</title>
    <link rel="icon" href="{{ asset('image/kotakia.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&family=Playfair+Display:ital,wght@0,700;1,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="catalog-page customer-page bg-slate-50 text-slate-900 antialiased">
    <a href="#main-content" class="sr-only focus:not-sr-only">Terus ke kandungan</a>
    <nav class="bg-white border-b border-slate-100" aria-label="Navigasi utama">
        <div class="max-w-7xl mx-auto px-5 sm:px-6 py-4 flex flex-wrap items-center justify-between gap-4">
            <a href="{{ url('/') }}" class="flex items-center gap-2 font-bold text-lg tracking-tight">
                <img src="{{ asset('image/kotakia.png') }}" alt="" class="w-9 h-9 object-contain">
                <span><span class="text-[#B6975A]">KOTAKIA</span>.MY</span>
            </a>
            <div class="flex items-center gap-4 sm:gap-6 text-sm font-semibold">
                <a href="{{ route('catalog') }}" class="hover:text-[#92743e]">Katalog</a>
                @auth
                    <a href="{{ auth()->user()->isManager() ? route('manager.index') : route('dashboard') }}" class="hover:text-[#92743e]">{{ auth()->user()->isManager() ? 'Portal Manager' : 'Dashboard' }}</a>
                    <a href="{{ route('profile.edit') }}" class="btn-secondary !py-2 !px-3 gap-2"><x-icon name="user" />Profil</a>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn-secondary !py-2 !px-3 gap-2 text-slate-500" type="submit"><x-icon name="logout" />Log Keluar</button></form>
                @else
                    <a href="{{ route('login') }}" class="text-[#92743e]">Log Masuk</a>
                @endauth
            </div>
        </div>
    </nav>
    <main id="main-content" class="max-w-7xl mx-auto px-5 sm:px-6 py-10 md:py-14 min-h-[70vh]">
        @if (session('status'))
            <div role="status" class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 text-emerald-900 px-5 py-4 text-sm">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div role="alert" class="mb-6 rounded-2xl border border-red-200 bg-red-50 text-red-900 px-5 py-4 text-sm">
                <p class="font-bold mb-2">Sila semak maklumat berikut:</p>
                <ul class="list-disc pl-5 space-y-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        @yield('content')
    </main>
    @include('partials.catalog-footer')
</body>
</html>
