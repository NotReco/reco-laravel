<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Đăng nhập' }} — {{ config('app.name', 'Reco') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen flex">
        {{-- Left side: Cinematic background --}}
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden">
            {{-- Gradient overlay --}}
            <div class="absolute inset-0 bg-gradient-to-br from-dark-950 via-dark-900/95 to-accent-900/30 z-10"></div>

            {{-- Background pattern --}}
            <div class="absolute inset-0 opacity-5 z-0"
                style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;0.4&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">
            </div>

            {{-- Content --}}
            <div class="relative z-20 flex flex-col justify-between p-12 w-full">
                {{-- Logo --}}
                <a href="/" class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-accent-500 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                        </svg>
                    </div>
                    <span class="text-2xl font-display font-bold text-white">Reco</span>
                </a>

                {{-- Middle quote --}}
                <div class="space-y-6">
                    <div class="w-12 h-1 bg-accent-500 rounded-full"></div>
                    <blockquote class="text-3xl font-display font-bold text-white leading-tight">
                        "Phim hay không chỉ để xem,<br>
                        <span class="text-gradient">mà để cảm nhận.</span>"
                    </blockquote>
                    <p class="text-dark-300 text-lg">
                        Khám phá thế giới điện ảnh, chia sẻ cảm xúc và nhận gợi ý thông minh từ AI.
                    </p>
                </div>

                {{-- Bottom stats --}}
                <div class="flex items-center gap-8">
                    <div>
                        <p class="text-2xl font-bold text-white">10K+</p>
                        <p class="text-dark-400 text-sm">Bộ phim</p>
                    </div>
                    <div class="w-px h-10 bg-dark-700"></div>
                    <div>
                        <p class="text-2xl font-bold text-white">50K+</p>
                        <p class="text-dark-400 text-sm">Đánh giá</p>
                    </div>
                    <div class="w-px h-10 bg-dark-700"></div>
                    <div>
                        <p class="text-2xl font-bold text-white">AI</p>
                        <p class="text-dark-400 text-sm">Gợi ý thông minh</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right side: Auth form --}}
        <div class="w-full lg:w-1/2 flex flex-col justify-center items-center p-6 sm:p-12 bg-dark-950">
            {{-- Mobile logo --}}
            <div class="lg:hidden mb-8">
                <a href="/" class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-accent-500 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                        </svg>
                    </div>
                    <span class="text-2xl font-display font-bold text-white">Reco</span>
                </a>
            </div>

            <div class="w-full max-w-md animate-fade-in">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>

</html>