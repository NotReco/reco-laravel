<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Đăng nhập' }} | {{ config('app.name', 'RecoDB') }}</title>

    {{-- Favicon & App Icons --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('web-app-manifest-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('web-app-manifest-512x512.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta property="og:image" content="{{ asset('storage/images/logo-og.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-50 text-gray-900 flex flex-col min-h-screen">
    
    {{-- ── Navbar ─────────────────────────────────────── --}}
    @include('partials.navbar')

    {{-- ── Main Content ────────────────────────────────── --}}
    <main class="flex-grow flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8 mt-16">
        
        {{-- Auth Card --}}
        <div class="w-full max-w-[420px] bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-8 py-10 sm:px-10">
                {{ $slot }}
            </div>
        </div>
        
    </main>

    {{-- ── Footer ──────────────────────────────────────── --}}
    @include('partials.footer')

    {{-- ── Toast Notifications ─────────────────────────── --}}
    @if(session('success') || session('error') || session('info'))
        <x-toast />
    @endif

</body>
</html>