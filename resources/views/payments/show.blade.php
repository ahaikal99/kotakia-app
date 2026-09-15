@extends('layouts.customer')
@section('title', 'Bayaran')
@section('content')
<div class="max-w-5xl mx-auto receipt-page">
    <div class="hidden print:block mb-8 border-b border-slate-300 pb-5">
        <p class="font-bold text-xl">KOTAKIA.MY</p>
        <p class="text-sm text-slate-500 mt-1">Resit Tempahan #KT{{ str_pad($invitation->id, 5, '0', STR_PAD_LEFT) }}</p>
    </div>
    <a href="{{ route('dashboard') }}" class="text-sm text-slate-500 no-print">← Dashboard</a>
    <h1 class="font-serif text-3xl md:text-4xl mt-6 mb-3 no-print">{{ $invitation->payment ? 'Butiran tempahan anda.' : 'Semak dan sahkan tempahan.' }}</h1>
    <p class="text-slate-500 mb-8 no-print">Semak ringkasan tempahan anda di bawah.</p>
    <div class="grid lg:grid-cols-5 gap-6 items-start">
        <section class="panel lg:col-span-3">
            <h2 class="text-xl font-bold mb-6">Ringkasan Tempahan</h2>
            <div class="flex gap-5 items-center mb-7">
                <a href="{{ asset('image/KATALOG/'.$invitation->design_code.'.png') }}" target="_blank" rel="noopener" aria-label="Lihat preview {{ $invitation->design_name }} di tab baharu" class="shrink-0">
                    <img src="{{ asset('image/KATALOG/'.$invitation->design_code.'.png') }}" alt="{{ $invitation->design_name }}" class="h-40 w-24 object-contain bg-[#f1eee8] rounded-xl p-2 hover:ring-2 hover:ring-[#B6975A] transition">
                </a>
                <div class="min-w-0"><span class="eyebrow">#KT{{ str_pad($invitation->id, 5, '0', STR_PAD_LEFT) }}</span><h3 class="font-serif text-2xl mt-3 break-words">{{ $invitation->title }}</h3><p class="text-sm text-slate-500 mt-2">{{ $invitation->design_name }} · {{ $invitation->design_code }}</p></div>
            </div>
            <dl class="order-details">
                <div><dt>Nama yang diraikan</dt><dd>{{ $invitation->celebrant_name }}</dd></div>
                <div><dt>Tuan rumah</dt><dd>{{ $invitation->host_name }}</dd></div>
                <div><dt>Tarikh & masa</dt><dd>{{ $invitation->event_date->format('d/m/Y') }} · {{ substr($invitation->start_time, 0, 5) }} @if($invitation->end_time) – {{ substr($invitation->end_time, 0, 5) }} @endif</dd></div>
                <div><dt>Lokasi</dt><dd>{{ $invitation->venue }}<br>{{ $invitation->address }}</dd></div>
                @if($invitation->map_url)<div><dt>Peta</dt><dd><a href="{{ $invitation->map_url }}" target="_blank" rel="noopener" class="underline">Lihat lokasi ↗</a></dd></div>@endif
                <div><dt>Hubungan</dt><dd>{{ $invitation->contact_name }}<br>{{ $invitation->contact_phone }}</dd></div>
                @if($invitation->message)<div><dt>Ucapan</dt><dd class="whitespace-pre-line">{{ $invitation->message }}</dd></div>@endif
                <div><dt>Pakej</dt><dd>{{ $invitation->package_name }}</dd></div>
            </dl>
            <div class="flex flex-wrap gap-3 mt-6 no-print">
                <a href="{{ route('invitations.edit', $invitation) }}" class="btn-secondary gap-2"><x-icon name="edit" />Edit Maklumat</a>
                <a href="{{ route('invitations.options', $invitation) }}" class="btn-secondary gap-2"><x-icon name="palette" />{{ $invitation->payment ? 'Tukar Design' : 'Ubah Pakej atau Design' }}</a>
            </div>
        </section>
        <section class="panel lg:col-span-2">
            <span class="eyebrow">Bayaran</span>
            <h2 class="text-xl font-bold mt-4 mb-5">{{ $invitation->payment ? 'Rekod Tempahan' : 'Jumlah Tempahan' }}</h2>
            <p class="text-4xl font-bold mb-6">RM{{ number_format($invitation->amount_cents / 100, 2) }}</p>
            <div class="rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-sm p-4 leading-relaxed mb-6 no-print">Bayaran dalam talian belum tersedia. Tempahan boleh direkodkan tanpa caj.</div>
            @if($invitation->payment)
                <div class="rounded-xl bg-emerald-50 p-4 text-sm text-emerald-900 mb-6">
                    <p class="font-bold mb-2">✓ Tempahan berjaya direkodkan</p>
                    <p class="break-all">{{ $invitation->payment->reference }}</p>
                    <p class="mt-2">{{ $invitation->payment->simulated_at->format('d/m/Y H:i') }}</p>
                </div>
                <div class="grid gap-3 no-print">
                    <a href="{{ route('receipts.show', $invitation) }}" target="_blank" rel="noopener" class="btn-secondary w-full gap-2" data-print-receipt><x-icon name="print" />Lihat & Cetak Resit</a>
                    <a href="{{ route('dashboard') }}" class="btn-primary w-full">Kembali ke Dashboard</a>
                </div>
            @else
                <form method="POST" action="{{ route('payments.simulate', $invitation) }}" class="no-print">
                    @csrf
                    <button type="submit" class="btn-primary w-full">Rekodkan Tempahan</button>
                </form>
                <a href="{{ route('dashboard') }}" class="block text-center mt-4 text-sm text-slate-500 underline no-print">Bayar kemudian</a>
            @endif
        </section>
    </div>
    @if($invitation->payment)
        @php($publicUrl = $invitation->publicUrl())
        @php($shareMessage = $invitation->whatsappMessage())
        <section class="panel mt-6 no-print">
            <div class="flex items-start gap-3 mb-6">
                <span class="rounded-xl bg-emerald-50 text-emerald-700 p-3"><x-icon name="message" /></span>
                <div><h2 class="text-xl font-bold">Kongsi melalui WhatsApp</h2><p class="text-sm text-slate-500 mt-1">Pautan dan mesej dijana daripada maklumat tempahan anda.</p></div>
            </div>
            <label for="public-invitation-link" class="block text-sm font-semibold mb-2">Pautan jemputan</label>
            <div class="flex flex-col sm:flex-row gap-3 mb-6">
                <input id="public-invitation-link" class="form-input" value="{{ $publicUrl }}" readonly>
                <button type="button" class="btn-secondary gap-2 shrink-0" data-copy-target="public-invitation-link"><x-icon name="copy" />Salin Pautan</button>
            </div>
            <label for="whatsapp-message" class="block text-sm font-semibold mb-2">Mesej WhatsApp</label>
            <textarea id="whatsapp-message" class="form-input leading-relaxed" rows="15" readonly>{{ $shareMessage }}</textarea>
            <div class="flex flex-col sm:flex-row gap-3 mt-5">
                <button type="button" class="btn-secondary gap-2" data-copy-target="whatsapp-message"><x-icon name="copy" />Salin Mesej</button>
                <a href="https://wa.me/?text={{ rawurlencode($shareMessage) }}" target="_blank" rel="noopener" class="btn-primary gap-2"><x-icon name="message" />Buka WhatsApp</a>
                <a href="{{ $publicUrl }}" target="_blank" rel="noopener" class="btn-secondary gap-2"><x-icon name="external" />Preview Jemputan</a>
            </div>
            <p class="text-sm text-emerald-700 font-semibold mt-4" data-copy-status role="status" aria-live="polite"></p>
        </section>
    @endif
</div>
@endsection

