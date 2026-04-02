@props(['title' => '', 'pageTitle' => 'Dashboard'])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="article-editor-upload-url" content="{{ route('admin.articles.editor-upload') }}">

    <title>@isset($title){{ $title }} | @endisset Admin | {{ config('app.name', 'RecoDB') }}</title>

    {{-- Favicon & App Icons --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('web-app-manifest-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('web-app-manifest-512x512.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta property="og:image" content="{{ asset('storage/images/logo-og.png') }}">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine.js Cloak --}}
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="font-sans antialiased bg-dark-950 text-dark-100">

    <div class="flex min-h-screen" x-data="{ sidebarOpen: true }">

        {{-- ══ SIDEBAR ═══════════════════════════════════════════════ --}}
        <aside class="fixed inset-y-0 left-0 z-40 flex flex-col bg-dark-900 border-r border-dark-800 transition-all duration-300"
               :class="sidebarOpen ? 'w-64' : 'w-16'"
               @mouseenter="sidebarOpen = true">

            {{-- Logo --}}
            <div class="flex items-center h-16 px-4 border-b border-dark-800 shrink-0">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
                    <div class="w-8 h-8 flex items-center justify-center shrink-0">
                        <img src="{{ asset('storage/images/logo-icon.svg') }}" alt="Logo" class="w-8 h-8">
                    </div>
                    <span class="text-lg font-bold text-white" x-show="sidebarOpen" x-transition>Admin</span>
                </a>
            </div>

            {{-- Nav --}}
            <nav class="flex-1 py-4 px-2 space-y-1 overflow-y-auto">
                @php
                    $nav = [
                        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>'],
                        ['label' => 'Phim', 'route' => 'admin.movies.index', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>'],
                        ['label' => 'Reviews', 'route' => 'admin.reviews.index', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>'],
                        ['label' => 'Tin tức', 'route' => 'admin.articles.index', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>'],
                        ['label' => 'Users', 'route' => 'admin.users.index', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>'],
                    ];
                @endphp

                @foreach($nav as $item)
                    @php $active = request()->routeIs($item['route'] . '*') || request()->routeIs($item['route']); @endphp
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                              {{ $active ? 'bg-rose-600/20 text-rose-400 border border-rose-500/30' : 'text-dark-400 hover:text-white hover:bg-dark-800 border border-transparent' }}">
                        <svg class="w-5 h-5 shrink-0 {{ $active ? 'text-rose-400' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $item['icon'] !!}</svg>
                        <span x-show="sidebarOpen" x-transition>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            {{-- Bottom: User + Back to Site --}}
            <div class="border-t border-dark-800 p-3 space-y-2">
                <a href="{{ route('home') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm text-dark-500 hover:text-white hover:bg-dark-800 transition-colors">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
                    <span x-show="sidebarOpen" x-transition>Về trang chính</span>
                </a>
                <div class="flex items-center gap-3 px-3 py-2" x-show="sidebarOpen" x-transition>
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-rose-500 to-rose-700 flex items-center justify-center overflow-hidden shrink-0 ring-2 ring-dark-700">
                        @if(Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar }}" alt="" class="w-full h-full object-cover">
                        @else
                            <span class="text-xs font-bold text-white">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-dark-500 truncate">{{ Auth::user()->role->label() }}</p>
                    </div>
                </div>
            </div>
        </aside>

        {{-- ══ MAIN CONTENT ══════════════════════════════════════════ --}}
        <main class="flex-1 transition-all duration-300"
              :class="sidebarOpen ? 'ml-64' : 'ml-16'">

            {{-- Top bar --}}
            <header class="sticky top-0 z-30 h-16 bg-dark-950/95 backdrop-blur-xl border-b border-dark-800 flex items-center px-6">
                <button @click="sidebarOpen = !sidebarOpen" class="text-dark-400 hover:text-white transition-colors mr-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h1 class="text-lg font-semibold text-white">{{ $pageTitle }}</h1>
            </header>

            {{-- Content --}}
            <div class="p-6">
                {{-- Lỗi validation (Laravel dùng $errors, không phải session error) --}}
                @if ($errors->any())
                    <div class="mb-5 rounded-xl border border-red-500/40 bg-red-500/10 px-4 py-3 text-sm text-red-100" role="alert">
                        <p class="font-semibold text-white mb-2">Không lưu được — vui lòng sửa các lỗi sau:</p>
                        <ul class="list-disc list-inside space-y-1 text-red-100/95">
                            @foreach ($errors->all() as $message)
                                <li>{{ $message }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Toast --}}
                @if(session('success') || session('error') || session('info'))
                    <x-toast />
                @endif

                {{ $slot }}
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>
