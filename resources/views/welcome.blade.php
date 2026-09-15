<!DOCTYPE html>
<html lang="ms">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" type="image/png" href="{{ asset('image/kotakia.png') }}">

    <link rel="apple-touch-icon" href="{{ asset('image/kotakia.png') }}">

    <x-seo title="Kotakia | Kad Jemputan Digital & Kad Kahwin Online"
        description="Cipta kad jemputan digital bersama Kotakia. Pilih design kad kahwin, lengkapkan maklumat majlis dan kongsi pautan jemputan melalui WhatsApp." />




    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&family=Playfair+Display:ital,wght@0,700;1,700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .font-serif {
            font-family: 'Playfair Display', serif;
        }

        html {
            scroll-behavior: smooth;
        }

        /* Animasi terapung untuk elemen highlight */
        .floating {
            animation: floating 3s ease-in-out infinite;
        }

        @keyframes floating {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        /* Glassmorphism effect */
        .glass {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        /* Memastikan anak panah swiper guna warna emas dan saiz yang betul */
        .swiper-button-next:after,
        .swiper-button-prev:after {
            color: #B6975A !important;
            font-size: 20px !important;
            /* Laraskan saiz anak panah di sini */
            font-weight: bold;
        }

        /* Jika anda mahu kedudukan anak panah lebih center */
        .swiper-button-next,
        .swiper-button-prev {
            background-color: white !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900 antialiased" data-home-page>

    <nav class="fixed w-full z-50 glass border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">

            <a href="/" class="flex items-center gap-3 group">
                <img src="{{ asset('image/kotakia.png') }}" alt="Logo" class="w-10 h-10 rounded-sm object-contain">

                <div class="text-xl font-bold tracking-tighter" style="color: #B6975A;">
                    KOTAKIA<span class="text-slate-900">.MY</span>
                </div>
            </a>

            <div class="hidden md:flex space-x-8 text-sm font-semibold text-slate-700">
                <a href="#ciri" class="hover:text-[#B6975A] transition">Ciri-ciri</a>
                <a href="#pakej" class="hover:text-[#B6975A] transition">Pakej</a>
                <a href="{{ route('catalog') }}" class="hover:text-[#B6975A] transition">Katalog</a>
            </div>

            <a href="{{ auth()->check() ? route('dashboard') : route('login') }}"
                class="text-white px-5 py-2 rounded-full text-sm font-semibold transition shadow-lg hover:brightness-110 active:scale-95"
                style="background-color: #B6975A; shadow-color: rgba(182, 151, 90, 0.3);">
                {{ auth()->check() ? 'Dashboard' : 'Log Masuk' }}
            </a>
        </div>
    </nav>

    <header class="pt-32 pb-16 px-6 overflow-hidden">
        <div class="max-w-4xl mx-auto text-center">
            <div data-aos="fade-down">
                <span class="inline-block px-4 py-1.5 mb-6 text-xs font-semibold tracking-widest uppercase rounded-full"
                    style="background-color: rgba(182, 151, 90, 0.1); color: #B6975A;">
                    Trend Jemputan 2026
                </span>
            </div>

            <h1 data-aos="fade-up" data-aos-delay="100" class="text-5xl md:text-7xl font-serif font-bold leading-tight mb-6">
                Abadikan Momen Indah Dengan
                <span class="italic" style="color: #B6975A;">E-Kad Digital</span>
            </h1>

            <p data-aos="fade-up" data-aos-delay="200" class="text-slate-600 text-lg md:text-xl mb-10 max-w-2xl mx-auto leading-relaxed">
                Sistem jemputan pintar yang memudahkan tetamu anda. Tanpa login, pantas, dan boleh diakses di mana-mana sahaja.
            </p>

            <div data-aos="fade-up" data-aos-delay="300" class="flex flex-col md:flex-row items-center justify-center gap-4">
                <a href="#pakej" class="w-full md:w-auto bg-slate-900 text-white px-8 py-4 rounded-2xl font-bold text-lg hover:shadow-2xl transition-all hover:-translate-y-1">Pilih Pakej</a>
                <a href="{{ route('catalog') }}" class="w-full md:w-auto bg-white border border-slate-200 px-8 py-4 rounded-2xl font-bold text-lg hover:bg-slate-50 transition">Lihat Katalog</a>
            </div>
            <div class="hero-card-scene" data-hero-scene>
                <div class="hero-card-art" aria-hidden="true">
                    <div class="hero-card-orbit"></div>
                    <div class="hero-preview hero-preview-left"><img src="{{ asset('image/KATALOG/MD002.png') }}" alt="" width="140" height="245"></div>
                    <div class="hero-preview hero-preview-right"><img src="{{ asset('image/KATALOG/MD003.png') }}" alt="" width="140" height="245"></div>
                    <div class="hero-preview hero-preview-center"><img src="{{ asset('image/KATALOG/MD001.png') }}" alt="" width="155" height="275"></div>
                    <span class="hero-charm hero-charm-heart"><i class="la la-heart"></i></span>
                    <span class="hero-charm hero-charm-mail"><i class="la la-envelope"></i></span>
                    <span class="hero-spark hero-spark-one">✦</span><span class="hero-spark hero-spark-two">✧</span>
                </div>
                <div class="hero-art-caption"><span>Satu jemputan. Seribu kenangan.</span></div>
            </div>
        </div>
    </header>

    <section id="ciri" class="py-24 bg-white px-6">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-12 text-center">
            <div class="p-8 rounded-3xl hover:bg-slate-50 transition duration-300" data-aos="zoom-in" data-aos-delay="100">
                <div class="w-20 h-20 bg-pink-50 text-pink-600 rounded-3xl flex items-center justify-center mx-auto text-3xl mb-6 shadow-inner">📱</div>
                <h3 class="text-xl font-bold mb-3">Mobile First</h3>
                <p class="text-slate-500 leading-relaxed">Paparan responsif yang dioptimumkan sepenuhnya untuk skrin telefon pintar tetamu anda.</p>
            </div>
            <div class="p-8 rounded-3xl hover:bg-slate-50 transition duration-300" data-aos="zoom-in" data-aos-delay="200">
                <div class="w-20 h-20 bg-blue-50 text-blue-600 rounded-3xl flex items-center justify-center mx-auto text-3xl mb-6 shadow-inner">⚡</div>
                <h3 class="text-xl font-bold mb-3">Akaun Pelanggan</h3>
                <p class="text-slate-500 leading-relaxed">Daftar dan log masuk untuk mencipta kad serta mengurus tempahan melalui dashboard anda.</p>
            </div>
            <div class="p-8 rounded-3xl hover:bg-slate-50 transition duration-300" data-aos="zoom-in" data-aos-delay="300">
                <div class="w-20 h-20 bg-green-50 text-green-600 rounded-3xl flex items-center justify-center mx-auto text-3xl mb-6 shadow-inner">🎵</div>
                <h3 class="text-xl font-bold mb-3">Lagu & Animasi</h3>
                <p class="text-slate-500 leading-relaxed">Suasana majlis lebih meriah dengan lagu latar pilihan dan kesan animasi yang lembut.</p>
            </div>
        </div>
    </section>

    <section id="pakej" class="py-28 px-6 bg-slate-50 overflow-hidden">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-20" data-aos="fade-down">
                <h2 class="text-4xl md:text-5xl font-serif font-bold mb-4">Pilih Pakej Anda</h2>
                <p class="text-slate-500 text-lg">Pilihan yang tepat untuk hari bahagia anda.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-center">

                <!-- PAKEJ MAWAR (BASIC) -->
                <div data-aos="fade-right" class="bg-white p-10 rounded-[2.5rem] border border-slate-200 shadow-sm hover:shadow-xl transition-all group">
                    <h4 class="font-bold text-lg mb-2 text-slate-500">Pakej Mawar</h4>
                    <div class="text-5xl font-bold mb-8">RM49 <span class="text-sm font-normal text-slate-400">/kad</span></div>
                    <ul class="space-y-4 mb-10 text-slate-600">
                        <li class="flex items-center gap-3">✅ RSVP</li>
                        <li class="flex items-center gap-3">✅ Contact</li>
                        <li class="flex items-center gap-3">✅ Kalendar</li>
                        <li class="flex items-center gap-3">✅ Muzik Latar</li>
                        <li class="flex items-center gap-3">✅ Lokasi (Waze/Maps)</li>
                        <li class="flex items-center gap-3">✅ Countdown Majlis</li>
                        <li class="flex items-center gap-3 text-slate-300">❌ Galeri Gambar</li>
                        <li class="flex items-center gap-3 text-slate-300">❌ Guestbook & Ucapan</li>
                        <li class="flex items-center gap-3 text-slate-300">❌ Money Gift (QR)</li>
                        <li class="flex items-center gap-3 text-slate-300">❌ Wishlist (10 Item)</li>
                    </ul>
                    <a href="/order?pakej=mawar" class="block text-center w-full py-4 bg-slate-100 group-hover:text-white rounded-2xl font-bold transition-all" onmouseover="this.style.backgroundColor='#B6975A'" onmouseout="this.style.backgroundColor=''">Pilih Mawar</a>
                </div>

                <!-- PAKEJ ORKID (PALING POPULAR) -->
                <div data-aos="fade-up" class="bg-slate-900 p-12 rounded-[2.5rem] border border-slate-800 shadow-2xl relative overflow-hidden transform md:scale-105 z-10">
                    <div class="absolute top-0 right-0 text-white text-[10px] px-8 py-2 font-bold uppercase tracking-widest rounded-bl-[2rem]" style="background-color: #B6975A;">
                        Best Seller
                    </div>

                    <h4 class="font-bold text-lg mb-2 uppercase tracking-widest" style="color: #B6975A;">Pakej Orkid</h4>
                    <div class="text-5xl font-bold mb-8 text-white">RM69 <span class="text-sm font-normal text-white/40">/kad</span></div>
                    <ul class="space-y-4 mb-10 text-white/80">
                        <li class="flex items-center gap-3">✅ RSVP</li>
                        <li class="flex items-center gap-3">✅ Contact</li>
                        <li class="flex items-center gap-3">✅ Kalendar</li>
                        <li class="flex items-center gap-3">✅ Muzik Latar</li>
                        <li class="flex items-center gap-3">✅ Lokasi (Waze/Maps)</li>
                        <li class="flex items-center gap-3">✅ Countdown Majlis</li>
                        <li class="flex items-center gap-3">✅ Galeri Gambar</li>
                        <li class="flex items-center gap-3">✅ Guestbook & Ucapan</li>
                        <li class="flex items-center gap-3 text-slate-300">❌ Money Gift (QR)</li>
                        <li class="flex items-center gap-3 text-slate-300">❌ Wishlist (10 Item) </li>
                    </ul>
                    <a href="/order?pakej=orkid" class="block text-center w-full py-4 text-white rounded-2xl font-bold transition-all shadow-lg active:scale-95"
                        style="background-color: #B6975A; box-shadow: 0 10px 15px -3px rgba(182, 151, 90, 0.4);">
                        Beli Sekarang
                    </a>
                </div>

                <!-- PAKEJ EKSKLUSIF (PREMIUM) -->
                <div data-aos="fade-left" class="bg-white p-10 rounded-[2.5rem] border border-slate-200 shadow-sm hover:shadow-xl transition-all group text-right relative overflow-hidden">

                    <!-- Label Premium dengan Curve di Bottom Left -->
                    <div class="bg-slate-900 absolute top-0 right-0 text-white text-[10px] px-8 py-2 font-bold uppercase tracking-widest rounded-bl-[2rem]" style="color: #B6975A;">
                        Premium
                    </div>

                    <h4 class="font-bold text-lg mb-2 text-slate-500 mt-4">Pakej Eksklusif</h4>
                    <div class="text-5xl font-bold mb-8" style="color: #B6975A;">RM89 <span class="text-sm font-normal text-slate-400">/kad</span></div>

                    <ul class="space-y-4 mb-10 text-slate-600">
                        <li class="flex items-center gap-3 justify-end">RSVP ✅</li>
                        <li class="flex items-center gap-3 justify-end">Contact ✅</li>
                        <li class="flex items-center gap-3 justify-end">Kalendar ✅</li>
                        <li class="flex items-center gap-3 justify-end">Muzik Latar ✅</li>
                        <li class="flex items-center gap-3 justify-end">Lokasi (Waze/Maps) ✅</li>
                        <li class="flex items-center gap-3 justify-end">Countdown Majlis ✅</li>
                        <li class="flex items-center gap-3 justify-end">Galeri Gambar ✅</li>
                        <li class="flex items-center gap-3 justify-end">Guestbook & Ucapan ✅</li>
                        <li class="flex items-center gap-3 justify-end">Money Gift (QR) ✅</li>
                        <li class="flex items-center gap-3 justify-end">Wishlist (10 Item) ✅</li>
                    </ul>

                    <a href="https://wa.me/601xxxxxxx" class="block text-center w-full py-4 bg-slate-900 text-white rounded-2xl font-bold transition-all hover:opacity-90" onmouseover="this.style.backgroundColor='#B6975A'" onmouseout="this.style.backgroundColor='#0f172a'">Hubungi Kami</a>
                </div>

            </div>

        </div>
    </section>

    @include('partials.home-details')

    <footer class="py-16 bg-white border-t border-slate-100 text-center">
        <div class="flex items-center justify-center gap-3 mb-4">
            <img src="{{ asset('image/kotakia.png') }}" alt="Logo" class="w-8 h-8 object-contain">
            <div class="text-xl font-bold tracking-tighter" style="color: #B6975A;">
                KOTAKIA<span class="text-slate-900">.MY</span>
            </div>
        </div>

        <p class="text-slate-400 text-sm mb-6 max-w-xs mx-auto leading-relaxed">
            Solusi e-kad jemputan digital terbaik di Malaysia.
        </p>

        <p class="text-slate-300 text-[10px] uppercase tracking-widest mb-1 hover:text-[#B6975A] transition-colors">
            © 2026 KOTAKIA DIGITAL SOLUTION. HAKCIPTA TERPELIHARA.
        </p>

        <p class="text-slate-300 text-[10px] tracking-widest hover:text-[#B6975A] transition-colors">
            <a href="/dasar-privasi">Dasar Privasi</a> | <a href="mailto:kotakiahq@gmail.com">kotakiahq@gmail.com</a>
        </p>
    </footer>

    <audio id="bgMusic" loop>
        <source src="{{ asset('audio/lagu-landing-page.mp3') }}" type="audio/mpeg">
    </audio>

    <div class="fixed bottom-6 right-6 z-[60]">
        <button id="musicToggle"
            class="bg-white/90 backdrop-blur-sm p-4 rounded-full shadow-2xl transition-all active:scale-90 group"
            style="border: 1px solid rgba(182, 151, 90, 0.2); color: #B6975A;">

            <svg id="playIcon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
            </svg>

            <svg id="pauseIcon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </button>
    </div>

    <script>
        // Music Toggle Logic
        const music = document.getElementById('bgMusic');
        const btn = document.getElementById('musicToggle');
        const playIcon = document.getElementById('playIcon');
        const pauseIcon = document.getElementById('pauseIcon');

        btn.addEventListener('click', function() {
            if (music.paused) {
                music.play();
                playIcon.classList.add('hidden');
                pauseIcon.classList.remove('hidden');
                btn.classList.add('bg-pink-600', 'text-white');
            } else {
                music.pause();
                playIcon.classList.remove('hidden');
                pauseIcon.classList.add('hidden');
                btn.classList.remove('bg-pink-600', 'text-white');
            }
        });

    </script>

</body>

</html>
