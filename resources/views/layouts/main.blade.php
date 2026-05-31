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

    {{-- ── Trailer Modal (Global - Alpine Store) ───────── --}}
    <div x-data
         x-show="$store.trailerModal.show"
         x-cloak
         style="display: none"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[60] bg-black/90 flex items-center justify-center p-4"
         @click.self="$store.trailerModal.close()"
         @keydown.escape.window="$store.trailerModal.close()">

        {{-- Close Button --}}
        <button @click="$store.trailerModal.close()"
            class="absolute top-4 right-4 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors z-10">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <div class="w-full max-w-4xl aspect-video rounded-2xl overflow-hidden shadow-2xl relative bg-black">

            {{-- IFrame --}}
            <iframe x-show="!$store.trailerModal.error && $store.trailerModal.show"
                :src="$store.trailerModal.url"
                class="w-full h-full" frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
                x-on:error="$store.trailerModal.error = true">
            </iframe>

            {{-- Fallback khi embed bị chặn --}}
            <div x-show="$store.trailerModal.error" style="display:none"
                 class="absolute inset-0 flex flex-col items-center justify-center gap-4 bg-gray-900 text-white text-center p-6 z-10">
                <svg class="w-14 h-14 text-gray-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M15 10l4.553-2.276A1 1 0 0121 8.723v6.554a1 1 0 01-1.447.894L15 14M3 8a1 1 0 011-1h8a1 1 0 011 1v8a1 1 0 01-1 1H4a1 1 0 01-1-1V8z"/>
                </svg>
                <p class="text-gray-300 text-sm font-medium">Trailer này không hỗ trợ phát nhúng. Bạn có thể xem trực tiếp trên YouTube.</p>
                <div class="flex items-center gap-3 flex-wrap justify-center">
                    <a :href="$store.trailerModal.youtubeUrl()" target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-colors shadow-lg">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19.59 6.69a4.83 4.83 0 01-3.77-2.75 12 12 0 00-10.54 0A4.83 4.83 0 011.5 6.69 44.32 44.32 0 000 12a44.32 44.32 0 001.5 5.31 4.83 4.83 0 003.78 2.75 12 12 0 0010.44 0 4.83 4.83 0 003.77-2.75A44.32 44.32 0 0024 12a44.32 44.32 0 00-4.41-5.31zM9.75 15.02V8.98L15.5 12l-5.75 3.02z"/>
                        </svg>
                        Mở trên YouTube
                    </a>
                    <template x-if="$store.trailerModal.hasNext()">
                        <button @click="$store.trailerModal.next()"
                           class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-gray-900 font-semibold rounded-xl transition-colors shadow-lg hover:bg-gray-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Thử trailer khác
                        </button>
                    </template>
                </div>
            </div>

            {{-- Floating action bar --}}
            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 z-20 flex gap-3"
                 x-show="$store.trailerModal.show && !$store.trailerModal.error">
                <template x-if="$store.trailerModal.hasNext()">
                    <button @click="$store.trailerModal.next()"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-black/60 hover:bg-black/80 backdrop-blur text-white text-sm font-semibold rounded-full border border-white/20 transition-colors shadow-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                        Video lỗi? Thử trailer khác
                    </button>
                </template>
                <template x-if="!$store.trailerModal.hasNext()">
                    <a :href="$store.trailerModal.youtubeUrl()" target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-black/60 hover:bg-black/80 backdrop-blur text-white text-sm font-semibold rounded-full border border-white/20 transition-colors shadow-lg">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19.59 6.69a4.83 4.83 0 01-3.77-2.75 12 12 0 00-10.54 0A4.83 4.83 0 011.5 6.69 44.32 44.32 0 000 12a44.32 44.32 0 001.5 5.31 4.83 4.83 0 003.78 2.75 12 12 0 0010.44 0 4.83 4.83 0 003.77-2.75A44.32 44.32 0 0024 12a44.32 44.32 0 00-4.41-5.31zM9.75 15.02V8.98L15.5 12l-5.75 3.02z"/>
                        </svg>
                        Video bị chặn? Xem trên YouTube
                    </a>
                </template>
            </div>
        </div>
    </div>

    {{-- Alpine Store: trailerModal --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('trailerModal', {
                show: false,
                url: '',
                error: false,
                candidates: [],
                index: 0,

                open(urlOrKey, candidates = []) {
                    this.error = false;
                    this.candidates = candidates || [];
                    this.index = 0;

                    if (candidates && candidates.length > 0) {
                        // Có candidates → dùng embed_url của candidate đầu tiên
                        this.url = candidates[0].embed_url;
                    } else if (urlOrKey) {
                        // Không có candidates → parse url/key
                        let videoId = '';
                        if (urlOrKey.includes('youtube.com/watch?v=')) {
                            try { videoId = new URL(urlOrKey).searchParams.get('v'); } catch(e) {}
                        } else if (urlOrKey.includes('youtube.com/embed/')) {
                            videoId = urlOrKey.split('youtube.com/embed/')[1].split('?')[0];
                        } else if (urlOrKey.includes('youtu.be/')) {
                            videoId = urlOrKey.split('youtu.be/')[1].split('?')[0];
                        } else if (/^[a-zA-Z0-9_-]{11}$/.test(urlOrKey)) {
                            videoId = urlOrKey;
                        }
                        this.url = videoId
                            ? `https://www.youtube.com/embed/${videoId}?autoplay=1&rel=0`
                            : urlOrKey;
                    }
                    this.show = true;
                    window.dispatchEvent(new CustomEvent('trailer-opened'));
                },

                close() {
                    this.show = false;
                    this.url = '';
                    this.error = false;
                    window.dispatchEvent(new CustomEvent('trailer-closed'));
                },

                next() {
                    if (this.index < this.candidates.length - 1) {
                        this.index++;
                        this.url = this.candidates[this.index].embed_url;
                        this.error = false;
                    }
                },

                hasNext() {
                    return this.candidates.length > this.index + 1;
                },

                youtubeUrl() {
                    return this.url
                        ? this.url.replace('/embed/', '/watch?v=').replace('?autoplay=1&rel=0', '')
                        : '#';
                }
            });
        });
    </script>

    {{-- ── Page Scripts ─────────────────────────────────── --}}
    @stack('scripts')

    {{-- ── Toast Notifications ─────────────────────────── --}}
    @if(session('success') || session('error') || session('info'))
        <x-toast />
    @endif

    {{-- ── Global Report Modal ─────────────────────────── --}}
    <x-report-modal />

    {{-- ── Age Confirmation Modal ──────────────────────── --}}
    <x-age-confirmation-modal />

    {{-- ── Verify Email Required Modal ──────────────────── --}}
    <div x-data="{ show: false, msg: '' }"
         @verify-email-required.window="show = true; msg = $event.detail.message || 'Bạn cần xác minh email trước khi sử dụng chức năng yêu thích.'"
         x-cloak>
        <div x-show="show" class="fixed inset-0 z-[10001] flex items-center justify-center px-4">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="show = false"></div>
            <div class="relative w-full max-w-md rounded-2xl bg-white border border-gray-200 shadow-2xl p-6">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Xác minh email</h3>
                        <p class="text-sm text-gray-600 leading-relaxed" x-text="msg"></p>
                    </div>
                    <button @click="show = false" class="shrink-0 w-7 h-7 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="mt-5 flex flex-col sm:flex-row gap-3 sm:justify-end">
                    <button @click="show = false"
                        class="px-4 py-2 rounded-xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                        Để sau
                    </button>
                    <a href="{{ route('verification.notice') }}"
                        class="px-4 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold transition-colors text-center">
                        Xác minh email ngay
                    </a>
                </div>
            </div>
        </div>
    </div>

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
