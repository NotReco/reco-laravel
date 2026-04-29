@props(['title' => '', 'pageTitle' => 'Tổng quan'])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="admin-panel">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="article-editor-upload-url" content="{{ route('admin.articles.editor-upload') }}">

    <title>
        @if (!empty($title))
            {{ $title }} |
        @elseif (!empty($pageTitle))
            {{ $pageTitle }} |
        @endif Admin Panel
    </title>

    {{-- Favicon & App Icons --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('web-app-manifest-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('web-app-manifest-512x512.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta property="og:image" content="https://i.ibb.co/ynjxvNhx/logo-dark.jpg">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine.js Cloak --}}
    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Pre-render sidebar state to prevent flash */
        .admin-sidebar {
            width: 16rem;
        }

        /* default: open */
        .admin-main {
            margin-left: 16rem;
        }

        html.sidebar-collapsed .admin-sidebar {
            width: 4rem;
            overflow: hidden;
        }

        html.sidebar-collapsed .admin-sidebar .sidebar-label {
            display: none !important;
        }

        html.sidebar-collapsed .admin-main {
            margin-left: 4rem;
        }

        .sidebar-label {
            transition: opacity 200ms ease;
        }

        /* Hide sidebar nav scrollbar — still scrollable via mousewheel */
        .admin-sidebar nav {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        .admin-sidebar nav::-webkit-scrollbar {
            display: none;
        }

        html.sidebar-ready .admin-sidebar,
        html.sidebar-ready .admin-main {
            transition: all 300ms ease;
        }
    </style>
    <script>
        // Runs synchronously BEFORE body renders — zero flash
        if (localStorage.getItem('sidebarOpen') === 'false') {
            document.documentElement.classList.add('sidebar-collapsed');
        }
    </script>
</head>

<body class="font-sans antialiased bg-dark-950 text-dark-100">

    <div class="flex min-h-screen" x-data="{ sidebarOpen: localStorage.getItem('sidebarOpen') !== 'false' }" x-init="$watch('sidebarOpen', val => {
        localStorage.setItem('sidebarOpen', val);
        document.documentElement.classList.toggle('sidebar-collapsed', !val);
    });
    setTimeout(() => document.documentElement.classList.add('sidebar-ready'), 100)">

        {{-- ══ SIDEBAR ═══════════════════════════════════════════════ --}}
        <aside
            class="admin-sidebar fixed inset-y-0 left-0 z-40 flex flex-col bg-dark-900 border-r border-dark-800 overflow-hidden">

            {{-- Logo --}}
            <div class="flex items-center h-16 px-4 border-b border-dark-800 shrink-0">
                <div class="flex items-center gap-2.5 cursor-pointer" onclick="window.location.href='{{ route('admin.dashboard') }}'">
                    <div class="w-8 h-8 flex items-center justify-center shrink-0">
                        <img src="{{ asset('storage/images/logo-icon.svg') }}" alt="Logo" class="w-8 h-8">
                    </div>
                    <span class="text-lg font-bold text-white whitespace-nowrap sidebar-label"
                        :class="sidebarOpen ? 'opacity-100' : 'opacity-0'">Admin Panel</span>
                </div>
            </div>

            {{-- Nav --}}
            <nav class="flex-1 py-4 px-2 space-y-1 overflow-y-auto overflow-x-hidden">
                @php
                    $nav = [
                        [
                            'label' => 'Tổng quan',
                            'route' => 'super.dashboard',
                            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>',
                        ],
                        [
                            'label' => 'Nhóm Quyền (RBAC)',
                            'route' => 'super.roles.index',
                            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>',
                        ],
                        [
                            'label' => 'Tài khoản Staff',
                            'route' => 'super.staff.index',
                            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>',
                        ],
                    ];
                @endphp

                @foreach ($nav as $item)
                    @php $active = request()->routeIs($item['route'] . '*') || request()->routeIs($item['route']); @endphp
                    <div onclick="window.location.href='{{ route($item['route']) }}'"
                        class="flex items-center rounded-xl text-sm font-medium transition-all cursor-pointer
                              {{ $active ? 'bg-indigo-600/20 text-indigo-400' : 'text-dark-400 hover:text-white hover:bg-dark-800' }}">
                        <div class="w-12 h-10 flex items-center justify-center shrink-0 {{ $active ? 'text-indigo-400' : '' }}">
                            <svg class="w-5 h-5 shrink-0" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">{!! $item['icon'] !!}</svg>
                        </div>
                        <span class="whitespace-nowrap sidebar-label flex-1 pr-3"
                            :class="sidebarOpen ? 'opacity-100' : 'opacity-0'">{{ $item['label'] }}</span>
                        @if (!empty($item['badge']) && $item['badge'] > 0)
                            <span class="sidebar-label mr-2 min-w-[20px] h-5 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center px-1"
                                :class="sidebarOpen ? 'opacity-100' : 'opacity-0'">
                                {{ $item['badge'] > 99 ? '99+' : $item['badge'] }}
                            </span>
                        @endif
                    </div>
                @endforeach
            </nav>

            {{-- Bottom: User + Back to Site --}}
            <div class="border-t border-dark-800 py-3 px-2 space-y-1">
                <div onclick="window.location.href='{{ route('admin.dashboard') }}'"
                    class="flex items-center rounded-xl text-sm text-dark-500 hover:text-white hover:bg-dark-800 transition-colors cursor-pointer">
                    <div class="w-12 h-10 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </div>
                    <span class="whitespace-nowrap sidebar-label pr-3" :class="sidebarOpen ? 'opacity-100' : 'opacity-0'">Về Control Panel</span>
                </div>
                <div class="flex items-center rounded-xl py-1 mt-1">
                    <div class="w-12 flex items-center justify-center shrink-0">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center overflow-hidden shrink-0 ring-2 ring-dark-700">
                            @if (Auth::user()->avatar)
                                <img src="{{ Auth::user()->avatar }}" alt="" class="w-full h-full object-cover">
                            @else
                                <span class="text-xs font-bold text-white">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="min-w-0 pr-3 sidebar-label" :class="sidebarOpen ? 'opacity-100' : 'opacity-0'">
                        <p class="text-xs font-medium text-white truncate whitespace-nowrap">{{ Auth::user()->name }}</p>
                        <p class="text-xs font-medium text-gray-400 truncate whitespace-nowrap">{{ Auth::user()->role->label() }}</p>
                    </div>
                </div>
            </div>
        </aside>

        {{-- ══ MAIN CONTENT ══════════════════════════════════════════ --}}
        <main class="admin-main flex-1 min-w-0">

            {{-- Top bar --}}
            <header
                class="sticky top-0 z-30 h-16 bg-dark-950/95 backdrop-blur-xl border-b border-dark-800 flex items-center px-6">
                <button @click="sidebarOpen = !sidebarOpen"
                    class="text-dark-400 hover:text-white transition-colors mr-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <h1 class="text-lg font-semibold text-white">{{ $pageTitle }}</h1>
            </header>

            {{-- Content --}}
            <div class="p-6">
                {{-- Lỗi validation (Laravel dùng $errors, không phải session error) --}}
                @if ($errors->any())
                    <div class="mb-5 rounded-xl border border-red-500/40 bg-red-500/10 px-4 py-3 text-sm text-red-100"
                        role="alert">
                        <p class="font-semibold text-white mb-2">Không lưu được — vui lòng sửa các lỗi sau:</p>
                        <ul class="list-disc list-inside space-y-1 text-red-100/95">
                            @foreach ($errors->all() as $message)
                                <li>{{ $message }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Toast --}}
                @if (session('success') || session('error') || session('info'))
                    <x-toast />
                @endif

                {{ $slot }}
            </div>
        </main>
    </div>

    @stack('scripts')
</body>

</html>
