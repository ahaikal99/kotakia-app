<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <x-seo title="Katalog Design Kad Kahwin & Jemputan Digital | Kotakia"
        description="Lihat koleksi design kad jemputan digital Kotakia. Bandingkan reka bentuk, nama dan kod design untuk memilih kad yang sesuai dengan majlis anda."
        path="/design" :index="$designs->isNotEmpty()" />
    <link rel="icon" type="image/png" href="{{ asset('image/kotakia.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&family=Playfair+Display:ital,wght@0,700;1,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="catalog-page bg-slate-50 text-slate-900 antialiased">
    @include('partials.catalog-nav')

    <main id="main-content">
        <header class="px-6 pt-28 pb-12 md:pt-32 md:pb-16 text-center">
            <div><span class="inline-block rounded-full bg-[#B6975A]/10 px-4 py-1.5 text-xs font-semibold uppercase tracking-widest text-[#92743e] mb-5">Koleksi Kad Jemputan</span></div>
            <h1 class="font-serif text-4xl md:text-6xl font-bold leading-tight">Setiap Majlis, <span class="block sm:inline italic text-[#B6975A]">Satu Cerita.</span></h1>
            <p class="mx-auto mt-6 max-w-xl text-slate-500 text-base md:text-lg leading-relaxed">Temui reka bentuk yang mencerminkan cerita anda.<br class="hidden sm:block"> Jemputan indah untuk momen yang paling bermakna.</p>
        </header>

        <section aria-label="Katalog reka bentuk" class="max-w-7xl mx-auto px-6 pb-20">
            <div class="rounded-3xl border border-slate-200 bg-white p-5 md:p-7 mb-9">
                <p class="text-xs uppercase tracking-widest font-bold text-slate-500 mb-4" id="theme-label">Pilih tema majlis</p>
                <nav aria-labelledby="theme-label" class="flex flex-wrap gap-2">
                    @foreach (['semua' => 'Semua Tema'] + $themes as $key => $label)
                        <a href="{{ route('catalog', ['tema' => $key, 'susun' => $sort]) }}"
                           @if ($theme === $key) aria-current="true" @endif
                           class="catalog-filter inline-flex items-center gap-2 rounded-xl border px-4 py-3 text-sm font-semibold transition {{ $theme === $key ? 'bg-slate-900 border-slate-900 text-white' : 'bg-white border-slate-200 text-slate-600 hover:border-[#B6975A] hover:text-[#92743e]' }}">
                            {{ $label }} <span class="text-xs opacity-60">{{ $key === 'semua' ? $total : ($counts[$key] ?? 0) }}</span>
                        </a>
                    @endforeach
                </nav>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-7">
                <div>
                    <h2 class="text-xl font-bold">{{ $themes[$theme] ?? 'Semua Reka Bentuk' }}</h2>
                    <p class="text-sm text-slate-500 mt-1">{{ $designs->count() }} reka bentuk untuk diterokai</p>
                </div>
                <form action="{{ route('catalog') }}" method="GET" class="flex items-center gap-2 flex-wrap text-sm">
                    <input type="hidden" name="tema" value="{{ $theme }}">
                    <label for="catalog-sort" class="text-slate-500">Susun:</label>
                    <select id="catalog-sort" name="susun" class="border border-slate-200 bg-white rounded-xl px-3 py-2.5">
                        <option value="kod" @selected($sort === 'kod')>Kod reka bentuk</option>
                        <option value="nama-az" @selected($sort === 'nama-az')>Nama: A–Z</option>
                        <option value="nama-za" @selected($sort === 'nama-za')>Nama: Z–A</option>
                    </select>
                    <button type="submit" class="rounded-xl bg-slate-900 text-white px-4 py-2.5 font-semibold hover:bg-slate-700">Susun</button>
                </form>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                @forelse ($designs as $design)
                    <article class="catalog-card group rounded-[2rem] border border-slate-200 bg-white p-3 shadow-sm transition hover:shadow-xl hover:border-[#B6975A]/50">
                        <a href="{{ asset('image/KATALOG/'.$design['code'].'.png') }}" target="_blank" rel="noopener" class="catalog-art block relative overflow-hidden rounded-[1.5rem] bg-[#f1eee8]" aria-label="Lihat imej {{ $design['name'] }}, {{ $design['code'] }} (tab baharu)">
                            <img src="{{ asset('image/KATALOG/'.$design['code'].'.png') }}" alt="Reka bentuk {{ $design['name'] }}" width="448" height="944" class="catalog-preview" @if ($loop->index > 2) loading="lazy" @endif>
                            <span class="absolute bottom-4 left-1/2 -translate-x-1/2 whitespace-nowrap bg-white/95 border border-slate-200 rounded-full py-2.5 px-5 text-xs font-bold shadow-sm transition group-hover:bg-slate-900 group-hover:text-white">Lihat Reka Bentuk ↗</span>
                        </a>
                        <div class="px-3 pt-5 pb-4">
                            <div class="flex items-center justify-between gap-3 mb-2">
                                <span class="text-[10px] uppercase tracking-widest text-[#92743e] font-bold">{{ $themes[$design['theme']] }}</span>
                                <span class="text-xs font-semibold text-slate-500 rounded-lg bg-slate-100 px-2.5 py-1">{{ $design['code'] }}</span>
                            </div>
                            <h3 class="font-serif text-2xl font-bold">{{ $design['name'] }}</h3>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-[2rem] border border-dashed border-slate-300 bg-white px-6 py-20 text-center">
                        <span aria-hidden="true" class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-[#B6975A]/10 text-3xl text-[#92743e] mb-5">✉</span>
                        <h3 class="font-serif text-2xl font-bold mb-3">Koleksi ini belum tersedia</h3>
                        <p class="text-slate-500 max-w-md mx-auto leading-relaxed mb-6">Belum ada reka bentuk untuk tema {{ $themes[$theme] ?? 'ini' }}. Terokai koleksi lain untuk inspirasi majlis anda.</p>
                        <a href="{{ route('catalog') }}" class="inline-block rounded-xl bg-slate-900 text-white px-6 py-3 font-semibold">Lihat Semua Reka Bentuk</a>
                    </div>
                @endforelse
            </div>

            <aside class="mt-16 rounded-[2rem] bg-slate-900 p-8 md:p-12 flex flex-col md:flex-row items-center justify-between gap-6 text-center md:text-left">
                <div>
                    <h2 class="font-serif text-2xl md:text-3xl text-white font-bold">Jadikan jemputan anda istimewa.</h2>
                    <p class="text-slate-400 mt-3 leading-relaxed">Kenali pakej kami dan pilih yang sesuai untuk majlis anda.</p>
                </div>
                <a href="{{ url('/') }}#pakej" class="shrink-0 rounded-xl bg-[#B6975A] text-white px-7 py-4 font-bold hover:brightness-110 transition">Lihat Pakej →</a>
            </aside>
        </section>
    </main>

    @include('partials.catalog-footer')
</body>
</html>
