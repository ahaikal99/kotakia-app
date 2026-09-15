@extends('layouts.customer')
@section('title', $title)
@section('content')
<div class="panel max-w-xl mx-auto py-12 text-center">
<span class="eyebrow">Makluman akses</span>
<h1 class="font-serif text-3xl mt-5 mb-5">{{ $title }}</h1>
<p class="text-slate-600 leading-relaxed whitespace-pre-line break-words">{{ $reason }}</p>
<p class="text-sm text-slate-500 mt-5">Sila cuba semula kemudian atau hubungi pihak Kotakia untuk bantuan.</p>
<a href="{{ url('/') }}" class="btn-primary mt-7">Kembali ke Utama</a>
</div>
@endsection

