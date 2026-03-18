<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@isset($title){{ $title }} | @endisset{{ config('app.name', 'RecoDB') }}</title>
    <meta name="description"
        content="@isset($description){{ $description }}@else RecoDB — Khám phá, đánh giá và chia sẻ cảm nhận về phim điện ảnh cùng cộng đồng.@endisset">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-white text-gray-800">

    {{-- ── Navbar ─────────────────────────────────────── --}}
    @include('partials.navbar')

    {{-- ── Main Content ────────────────────────────────── --}}
    <main class="min-h-screen pt-16">
        {{ $slot }}
    </main>

    {{-- ── Footer ──────────────────────────────────────── --}}
    @include('partials.footer')

    {{-- ── Toast Notifications ─────────────────────────── --}}
    @if(session('success') || session('error') || session('info'))
        <x-toast />
    @endif

</body>

</html>