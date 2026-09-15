<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $invitation->title }} — KOTAKIA.MY</title>
    <link rel="icon" href="{{ asset('image/kotakia.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&family=Playfair+Display:ital,wght@0,700;1,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="catalog-page bg-[#f8f6f1] text-slate-900 antialiased">
    <main class="min-h-screen py-10 px-5">
        <article class="max-w-4xl mx-auto overflow-hidden rounded-[2rem] bg-white border border-[#dfd4be] shadow-xl">
            <div class="grid md:grid-cols-2">
                <div class="bg-[#eee9df] p-8 flex items-center justify-center">
                    <img src="{{ asset('image/KATALOG/'.$invitation->design_code.'.png') }}" alt="Design {{ $invitation->design_name }}" class="w-full max-h-[680px] object-contain drop-shadow-xl">
                </div>
                <div class="p-7 sm:p-10 flex flex-col justify-center text-center">
                    <img src="{{ asset('image/kotakia.png') }}" alt="" class="w-12 h-12 object-contain mx-auto mb-5">
                    <span class="eyebrow">Jemputan Majlis</span>
                    <h1 class="font-serif text-3xl sm:text-4xl mt-4 break-words">{{ $invitation->title }}</h1>
                    <p class="text-slate-500 mt-5">Dengan segala hormatnya</p>
                    <p class="font-semibold text-lg mt-2 break-words">{{ $invitation->host_name }}</p>
                    <p class="text-slate-500 mt-2">menjemput anda bagi meraikan</p>
                    <p class="font-serif text-2xl text-[#92743e] mt-3 break-words">{{ $invitation->celebrant_name }}</p>

                    <dl class="mt-8 space-y-5 text-left">
                        <div class="rounded-2xl bg-slate-50 p-4"><dt class="eyebrow">Tarikh & Masa</dt><dd class="font-semibold mt-2">{{ $invitation->event_date->locale('ms')->translatedFormat('l, j F Y') }}<br>{{ \Illuminate\Support\Carbon::createFromFormat('H:i', substr($invitation->start_time, 0, 5))->format('g:i A') }}@if($invitation->end_time) – {{ \Illuminate\Support\Carbon::createFromFormat('H:i', substr($invitation->end_time, 0, 5))->format('g:i A') }}@endif</dd></div>
                        <div class="rounded-2xl bg-slate-50 p-4"><dt class="eyebrow">Tempat</dt><dd class="font-semibold mt-2 break-words">{{ $invitation->venue }}</dd><dd class="text-sm text-slate-500 mt-1 whitespace-pre-line break-words">{{ $invitation->address }}</dd></div>
                    </dl>

                    @if($invitation->message)<p class="mt-7 text-sm text-slate-600 whitespace-pre-line leading-relaxed break-words">{{ $invitation->message }}</p>@endif
                    <div class="flex flex-col sm:flex-row justify-center gap-3 mt-8">
                        @if($invitation->map_url)<a href="{{ $invitation->map_url }}" target="_blank" rel="noopener" class="btn-primary">Lihat Lokasi</a>@endif
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $invitation->contact_phone) }}" class="btn-secondary">Hubungi {{ $invitation->contact_name }}</a>
                    </div>
                </div>
            </div>
        </article>
        <p class="text-center text-xs text-slate-400 mt-8">Jemputan digital oleh KOTAKIA.MY</p>
    </main>
</body>
</html>
