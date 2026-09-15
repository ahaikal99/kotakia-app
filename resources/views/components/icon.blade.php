@props(['name' => 'grid'])
@php
$paths = [
'image' => ['M3 3h18v18H3zM3 17l6-6 4 4 3-3 5 5', 'M9 7h.01'],
'close' => ['m6 6 12 12M6 18 18 6'],
'grid' => ['M3 3h7v7H3zM14 3h7v7h-7zM3 14h7v7H3zM14 14h7v7h-7z'],
'users' => ['M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M16 3a4 4 0 0 1 0 8M22 21v-2a4 4 0 0 0-3-3.87', 'M13 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0'],
'shield' => ['M12 3 3 7v5c0 5 9 9 9 9s9-4 9-9V7z', 'm8 12 3 3 5-6'],
'orders' => ['M6 3h12v18l-3-2-3 2-3-2-3 2zM9 7h6M9 11h6M9 15h3'],
'logs' => ['M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8zM14 2v6h6M8 12h8M8 16h8'],
'lock' => ['M5 10h14v11H5zM8 10V6a4 4 0 0 1 8 0v4M12 14v3'],
'logout' => ['M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M9 12h12m-4-4 4 4-4 4'],
'external' => ['M15 3h6v6M10 14 21 3M21 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5'],
'menu' => ['M4 6h16M4 12h16M4 18h16'],
'wallet' => ['M20 8V5a2 2 0 0 0-2-2H5a3 3 0 0 0 0 6h16v12H5a3 3 0 0 1-3-3V6M21 12h-5v5h5M17 14.5h.01'],
'clock' => ['M22 12a10 10 0 1 1-20 0 10 10 0 0 1 20 0M12 6v6l4 2'],
'search' => ['M21 21l-5-5M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0'],
'plus' => ['M12 5v14M5 12h14'],
'eye' => ['M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7', 'M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0'],
'save' => ['M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h12l4 4v12a2 2 0 0 1-2 2M7 3v6h10V3M7 21v-8h10v8'],
'check' => ['m5 12 4 4L19 6'],
'arrow' => ['M4 12h16m-6-6 6 6-6 6'],
'home' => ['m3 10 9-7 9 7M5 9v12h14V9M9 21v-8h6v8'],
'user' => ['M20 21a8 8 0 0 0-16 0', 'M12 13a5 5 0 1 0 0-10 5 5 0 0 0 0 10'],
'print' => ['M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2', 'M6 14h12v8H6z'],
'edit' => ['M12 20h9', 'M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4z'],
'palette' => ['M12 22a10 10 0 1 1 10-10c0 3-2 10-10 10', 'M7 11h.01M9 7h.01M14 7h.01M17 11h.01'],
'copy' => ['M8 8h12v12H8z', 'M16 8V4H4v12h4'],
'message' => ['M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z', 'M8 9h8M8 13h5'],
];
@endphp
<svg {{ $attributes->class(['w-5 h-5 shrink-0']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
@foreach($paths[$name] ?? $paths['grid'] as $path)<path d="{{ $path }}"/>@endforeach
</svg>

