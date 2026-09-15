    <nav class="fixed w-full z-50 glass border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">

            <a href="/" class="flex items-center gap-3 group">
                <img src="{{ asset('image/kotakia.png') }}" alt="Logo" class="w-10 h-10 rounded-sm object-contain">

                <div class="text-xl font-bold tracking-tighter" style="color: #B6975A;">
                    KOTAKIA<span class="text-slate-900">.MY</span>
                </div>
            </a>

            <div class="hidden md:flex space-x-8 text-sm font-semibold text-slate-700">
                <a href="{{ url('/') }}#ciri" class="hover:text-[#B6975A] transition">Ciri-ciri</a>
                <a href="{{ url('/') }}#pakej" class="hover:text-[#B6975A] transition">Pakej</a>
                <a href="{{ route('catalog') }}" aria-current="page" class="text-[#92743e] transition">Katalog</a>
            </div>

            <a href="{{ auth()->check() ? route('dashboard') : route('login') }}"
                class="text-white px-5 py-2 rounded-full text-sm font-semibold transition shadow-lg hover:brightness-110 active:scale-95"
                style="background-color: #B6975A; shadow-color: rgba(182, 151, 90, 0.3);">
                {{ auth()->check() ? 'Dashboard' : 'Log Masuk' }}
            </a>
        </div>
    </nav>
