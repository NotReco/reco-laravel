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
    <meta property="og:image" content="https://i.ibb.co/ynjxvNhx/logo-dark.jpg">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine.js Cloak & Top Progress Bar --}}
    <style>
        [x-cloak] { display: none !important; }

        /* Modern Top Progress Bar (NProgress style) */
        #top-progress-bar {
            position: fixed;
            top: 0;
            left: 0;
            width: 0%;
            height: 3px;
            background: #0ea5e9; /* Sky 500 */
            z-index: 99999;
            transition: width 0.4s ease, opacity 0.4s ease;
            opacity: 0;
            pointer-events: none;
            box-shadow: 0 0 10px #0ea5e9, 0 0 5px #0ea5e9;
        }
    </style>
</head>

<body class="font-sans antialiased bg-white text-gray-800">

    {{-- Top Progress Bar --}}
    <div id="top-progress-bar"></div>
    <script>
        (function() {
            var progressBar = document.getElementById('top-progress-bar');
            var progressInterval;

            // 1. Finish and hide the progress bar when the new page is finally loaded or restored
            function finishProgress() {
                if (!progressBar) return;
                clearInterval(progressInterval);
                progressBar.style.width = '100%';
                setTimeout(function() {
                    progressBar.style.opacity = '0';
                    setTimeout(function() {
                        progressBar.style.width = '0%';
                    }, 400); // Wait for opacity transition
                }, 200);
            }

            window.addEventListener('load', finishProgress);
            window.addEventListener('pageshow', function(event) {
                if (event.persisted) finishProgress();
            });

            // 2. Start the progress bar when clicking internal links
            document.addEventListener('click', function(e) {
                var target = e.target.closest('a');
                if (target && target.href && !target.hasAttribute('download') && target.target !== '_blank') {
                    try {
                        var url = new URL(target.href);
                        if (url.origin === window.location.origin) {
                            if (url.pathname === window.location.pathname && target.href.includes('#')) {
                                return;
                            }
                            
                            // Start animation
                            clearInterval(progressInterval);
                            progressBar.style.opacity = '1';
                            progressBar.style.width = '0%';
                            
                            // Force reflow
                            void progressBar.offsetWidth;
                            
                            // Slowly animate to 85%
                            var width = 10;
                            progressBar.style.width = width + '%';
                            
                            progressInterval = setInterval(function() {
                                if (width >= 85) {
                                    clearInterval(progressInterval);
                                    return;
                                }
                                // Slower increment as it gets closer to 85%
                                var increment = Math.random() * 5 + 1;
                                if (width > 60) increment = Math.random() * 2;
                                width += increment;
                                progressBar.style.width = width + '%';
                            }, 300);
                        }
                    } catch(err) {}
                }
            });
        })();
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

    {{-- ── Global Report Modal ─────────────────────────── --}}
    <x-report-modal />

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
