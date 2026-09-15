@props(['title', 'description', 'path' => '/', 'index' => true])
@php
    $siteUrl = rtrim(config('seo.url'), '/');
    $canonical = $siteUrl.$path;
    $imageUrl = $siteUrl.'/image/kotakia.png';
@endphp
<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}">
<meta name="robots" content="{{ $index ? 'index, follow, max-image-preview:large' : 'noindex, follow' }}">
<link rel="canonical" href="{{ $canonical }}">
<meta property="og:type" content="website">
<meta property="og:locale" content="ms_MY">
<meta property="og:site_name" content="Kotakia">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:image" content="{{ $imageUrl }}">
<meta property="og:image:alt" content="Logo Kotakia">
<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $imageUrl }}">
@if(config('seo.google_verification'))
<meta name="google-site-verification" content="{{ config('seo.google_verification') }}">
@endif
@if($path === '/')
@php
    $schema = [
        '@context' => 'https://schema.org',
        '@graph' => [
            ['@type' => 'Organization', '@id' => $siteUrl.'/#organization', 'name' => 'Kotakia', 'url' => $siteUrl.'/', 'logo' => $imageUrl],
            ['@type' => 'WebSite', '@id' => $siteUrl.'/#website', 'name' => 'Kotakia', 'alternateName' => 'KOTAKIA.MY', 'url' => $siteUrl.'/', 'inLanguage' => 'ms-MY', 'publisher' => ['@id' => $siteUrl.'/#organization']],
        ],
    ];
@endphp
<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
@endif
