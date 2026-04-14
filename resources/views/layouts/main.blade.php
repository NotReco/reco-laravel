<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@isset($title){{ $title }} | @endisset{{ config('app.name', 'RecoDB') }}</title>
    <meta name="description"
        content="@isset($description){{ $description }}@else RecoDB — Khám phá, đánh giá và chia sẻ cảm nhận về phim điện ảnh cùng cộng đồng.@endisset">

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
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine.js Cloak --}}
    <style>
        [x-cloak] { display: none !important; }

        /* Page Loading Overlay – masks ALL FOUC until window.onload */
        #page-loader {
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity .3s ease;
        }
        #page-loader.fade-out {
            opacity: 0;
            pointer-events: none;
        }
        #page-loader .spinner {
            width: 28px;
            height: 28px;
            border: 3px solid #e5e7eb;
            border-top-color: #0ea5e9;
            border-radius: 50%;
            animation: spin .6s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>

<body class="font-sans antialiased bg-white text-gray-800">

    {{-- Page Loading Overlay --}}
    <div id="page-loader"><div class="spinner"></div></div>
    <script>
        // 1. Hide loader when page is fully loaded
        window.addEventListener('load', function() {
            var loader = document.getElementById('page-loader');
            if (loader) {
                loader.classList.add('fade-out');
                // Optional: completely remove it after fade out
                setTimeout(function() { loader.style.display = 'none'; }, 350);
            }
        });

        // 2. Show loader IMMEDIATELY when clicking internal navigation links 
        // to prevent the "frozen" feeling while waiting for server response
        document.addEventListener('click', function(e) {
            var target = e.target.closest('a');
            if (target && target.href && !target.hasAttribute('download') && target.target !== '_blank') {
                try {
                    var url = new URL(target.href);
                    // Only trigger for internal links that are not hash links (#)
                    if (url.origin === window.location.origin) {
                        // Skip if it's just a hash/anchor link on the same page
                        if (url.pathname === window.location.pathname && target.href.includes('#')) {
                            return;
                        }
                        
                        // Show the loader
                        var loader = document.getElementById('page-loader');
                        if (loader) {
                            loader.style.display = 'flex';
                            // Force reflow
                            void loader.offsetWidth;
                            loader.classList.remove('fade-out');
                        }
                    }
                } catch(err) {
                    // Ignore URL parsing errors
                }
            }
        });
        
        // 3. Fallback: hide loader when user navigates back using browser buttons (BFCache)
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) { // If restored from Back-Forward Cache
                var loader = document.getElementById('page-loader');
                if (loader) {
                    loader.classList.add('fade-out');
                    setTimeout(function() { loader.style.display = 'none'; }, 350);
                }
            }
        });
    </script>

    {{-- ── Navbar ─────────────────────────────────────── --}}
    @include('partials.navbar')

    {{-- ── Main Content ────────────────────────────────── --}}
    <main class="min-h-screen pt-16">
        {{ $slot }}
    </main>

    {{-- ── Footer ──────────────────────────────────────── --}}
    @include('partials.footer')

    {{-- ── Page Scripts ─────────────────────────────────── --}}
    @stack('scripts')

    {{-- ── Toast Notifications ─────────────────────────── --}}
    @if(session('success') || session('error') || session('info'))
        <x-toast />
    @endif

    {{-- ── 2FA Remember Login Prompt ───────────────────── --}}
    @if(session('2fa_remember_prompt'))
        <div x-data="{ open: true }" x-cloak>
            <div x-show="open" class="fixed inset-0 z-[10000] flex items-center justify-center px-4">
                <div class="absolute inset-0 bg-black/50" x-on:click="open = false"></div>

                <div class="relative w-full max-w-lg rounded-2xl bg-white border border-gray-200 shadow-2xl p-6">
                    <h3 class="text-xl font-display font-bold text-gray-900">Tin cậy thiết bị này?</h3>
                    <p class="mt-2 text-sm text-gray-600">
                        Lưu đăng nhập để bạn không cần nhập mã xác thực vào lần tới.
                    </p>

                    <div class="mt-6 flex flex-col sm:flex-row gap-3 sm:justify-end">
                        <form method="post" action="{{ route('2fa.dismissTrust') }}">
                            @csrf
                            <button type="submit" class="btn-ghost w-full sm:w-auto italic" x-on:click="open = false">Không, cảm ơn</button>
                        </form>

                        <form method="post" action="{{ route('2fa.trustDevice') }}">
                            @csrf
                            <button type="submit" class="btn-primary w-full sm:w-auto">Tin cậy thiết bị</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @stack('scripts')
</body>

</html>
