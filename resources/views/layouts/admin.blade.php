@props(['title' => '', 'pageTitle' => 'Dashboard'])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

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
    <meta property="og:image" content="{{ asset('storage/images/logo-og.png') }}">

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
            opacity: 0 !important;
            pointer-events: none;
        }

        html.sidebar-collapsed .admin-main {
            margin-left: 4rem;
        }

        .sidebar-label {
            transition: opacity 200ms ease;
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
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
                    <div class="w-8 h-8 flex items-center justify-center shrink-0">
                        <img src="{{ asset('storage/images/logo-icon.svg') }}" alt="Logo" class="w-8 h-8">
                    </div>
                    <span class="text-lg font-bold text-white whitespace-nowrap sidebar-label"
                        :class="sidebarOpen ? 'opacity-100' : 'opacity-0'">Admin Panel</span>
                </a>
            </div>

            {{-- Nav --}}
            <nav class="flex-1 py-4 px-2 space-y-1 overflow-y-auto overflow-x-hidden">
                @php
                    $nav = [
                        [
                            'label' => 'Dashboard',
                            'route' => 'admin.dashboard',
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>',
                        ],
                        [
                            'label' => 'Phim',
                            'route' => 'admin.movies.index',
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>',
                        ],
                        [
                            'label' => 'Đánh giá',
                            'route' => 'admin.reviews.index',
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>',
                        ],
                        [
                            'label' => 'Tin tức',
                            'route' => 'admin.articles.index',
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>',
                        ],
                        [
                            'label' => 'Diễn đàn',
                            'route' => 'admin.forum-categories.index',
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>',
                        ],
                        [
                            'label' => 'Người dùng',
                            'route' => 'admin.users.index',
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>',
                        ],
                        [
                            'label' => 'Danh hiệu',
                            'route' => 'admin.user-titles.index',
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                        ],
                        [
                            'label' => 'Khung Avatar',
                            'route' => 'admin.avatar-frames.index',
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>',
                        ],
                    ];
                @endphp

                @foreach ($nav as $item)
                    @php $active = request()->routeIs($item['route'] . '*') || request()->routeIs($item['route']); @endphp
                    <a href="{{ route($item['route']) }}"
                        class="flex items-center rounded-xl text-sm font-medium transition-all
                              {{ $active ? 'bg-sky-600/20 text-sky-400 border border-sky-500/30' : 'text-dark-400 hover:text-white hover:bg-dark-800 border border-transparent' }}">
                        <div class="w-12 h-10 flex items-center justify-center shrink-0 {{ $active ? 'text-sky-400' : '' }}">
                            <svg class="w-5 h-5 shrink-0" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">{!! $item['icon'] !!}</svg>
                        </div>
                        <span class="whitespace-nowrap sidebar-label pr-3"
                            :class="sidebarOpen ? 'opacity-100' : 'opacity-0'">{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            {{-- Bottom: User + Back to Site --}}
            <div class="border-t border-dark-800 py-3 px-2 space-y-1">
                <a href="{{ route('home') }}"
                    class="flex items-center rounded-xl text-sm text-dark-500 hover:text-white hover:bg-dark-800 transition-colors">
                    <div class="w-12 h-10 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </div>
                    <span class="whitespace-nowrap sidebar-label pr-3" :class="sidebarOpen ? 'opacity-100' : 'opacity-0'">Về
                        trang chính</span>
                </a>
                <div class="flex items-center rounded-xl py-1 mt-1">
                    <div class="w-12 flex items-center justify-center shrink-0">
                        <div
                            class="w-8 h-8 rounded-full bg-gradient-to-br from-sky-500 to-sky-700 flex items-center justify-center overflow-hidden shrink-0 ring-2 ring-dark-700">
                            @if (Auth::user()->avatar)
                                <img src="{{ Auth::user()->avatar }}" alt="" class="w-full h-full object-cover">
                            @else
                                <span
                                    class="text-xs font-bold text-white">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="min-w-0 pr-3 sidebar-label" :class="sidebarOpen ? 'opacity-100' : 'opacity-0'">
                        <p class="text-xs font-medium text-white truncate whitespace-nowrap">{{ Auth::user()->name }}
                        </p>
                        <p class="text-xs font-medium text-gray-400 truncate whitespace-nowrap">
                            {{ Auth::user()->role->label() }}</p>
                    </div>
                </div>
            </div>
        </aside>

        {{-- ══ MAIN CONTENT ══════════════════════════════════════════ --}}
        <main class="admin-main flex-1">

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
