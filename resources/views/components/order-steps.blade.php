@props(['step' => 1])
<ol aria-label="Langkah tempahan" class="grid grid-cols-3 gap-2 mb-8">
    @foreach ([1 => 'Maklumat Kad', 2 => 'Pakej & Design', 3 => 'Bayaran'] as $number => $label)
        <li @if($step === $number) aria-current="step" @endif class="flex flex-col sm:flex-row items-center gap-2 sm:gap-3 rounded-2xl border px-2 py-4 text-center text-xs sm:text-sm {{ $step === $number ? 'bg-slate-900 border-slate-900 text-white' : 'bg-white border-slate-200 text-slate-500' }}">
            <span class="rounded-full w-7 h-7 flex items-center justify-center shrink-0 {{ $step === $number ? 'bg-[#B6975A] text-white' : 'bg-slate-100' }}">{{ $number }}</span>
            <span class="font-semibold">{{ $label }}</span>
        </li>
    @endforeach
</ol>

