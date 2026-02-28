<x-base-layout>
    {{-- Navigation --}}
    <nav class="fixed top-0 left-0 right-0 z-50 glass">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                {{-- Logo --}}
                <a href="/" class="flex items-center gap-3 group">
                    <div
                        class="w-9 h-9 bg-accent-500 rounded-lg flex items-center justify-center group-hover:bg-accent-600 transition-colors">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                        </svg>
                    </div>
                    <span class="text-xl font-display font-bold text-white">Reco</span>
                </a>

                {{-- Nav Links --}}
                <div class="hidden md:flex items-center gap-8">
                    <a href="#features"
                        class="text-dark-300 hover:text-white transition-colors text-sm font-medium">Tính năng</a>
                    <a href="#discover"
                        class="text-dark-300 hover:text-white transition-colors text-sm font-medium">Khám phá</a>
                    <a href="#ai" class="text-dark-300 hover:text-white transition-colors text-sm font-medium">AI Gợi
                        ý</a>
                </div>

                {{-- Auth Buttons --}}
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-primary text-sm !py-2 !px-4">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}"
                            class="text-dark-200 hover:text-white transition-colors text-sm font-medium">Đăng nhập</a>
                        <a href="{{ route('register') }}" class="btn-primary text-sm !py-2 !px-4">Đăng ký</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Hero Section --}}
    <section class="relative min-h-screen flex items-center overflow-hidden">
        {{-- Background effects --}}
        <div class="absolute inset-0">
            <div class="absolute top-1/4 -left-32 w-96 h-96 bg-accent-500/10 rounded-full blur-3xl animate-float"></div>
            <div class="absolute bottom-1/4 -right-32 w-96 h-96 bg-accent-600/5 rounded-full blur-3xl animate-float"
                style="animation-delay: -3s;"></div>
            <div
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-accent-500/5 rounded-full blur-3xl">
            </div>
        </div>

        {{-- Grid pattern --}}
        <div class="absolute inset-0 opacity-[0.03]"
            style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2240%22 height=%2240%22 viewBox=%220 0 40 40%22%3E%3Crect width=%2240%22 height=%2240%22 fill=%22none%22 stroke=%22%23ffffff%22 stroke-width=%220.5%22/%3E%3C/svg%3E');">
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-16">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                {{-- Left: Text Content --}}
                <div class="space-y-8 animate-fade-in">
                    <div
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-accent-500/10 border border-accent-500/20">
                        <span class="w-2 h-2 bg-accent-500 rounded-full animate-pulse"></span>
                        <span class="text-accent-400 text-sm font-medium">Tích hợp AI gợi ý thông minh</span>
                    </div>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-display font-extrabold text-white leading-tight">
                        Khám phá phim.<br>
                        <span class="text-gradient">Chia sẻ cảm xúc.</span>
                    </h1>

                    <p class="text-lg text-dark-300 max-w-lg leading-relaxed">
                        Cổng thông tin đánh giá phim điện ảnh hàng đầu. Viết review, chấm điểm và nhận gợi ý phim phù
                        hợp từ trợ lý AI thông minh.
                    </p>

                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('register') }}" class="btn-primary text-base !px-8 !py-4">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Bắt đầu ngay
                        </a>
                        <a href="#features" class="btn-secondary text-base !px-8 !py-4">
                            Tìm hiểu thêm
                        </a>
                    </div>

                    {{-- Social proof --}}
                    <div class="flex items-center gap-6 pt-4">
                        <div class="flex -space-x-3">
                            @for ($i = 0; $i < 4; $i++)
                                <div
                                    class="w-10 h-10 rounded-full bg-dark-{{ 600 + $i * 50 }} border-2 border-dark-950 flex items-center justify-center">
                                    <span class="text-xs text-dark-300">{{ ['👤', '🎬', '⭐', '🎭'][$i] }}</span>
                                </div>
                            @endfor
                        </div>
                        <div>
                            <p class="text-sm text-dark-200 font-medium">2,000+ người dùng</p>
                            <p class="text-xs text-dark-400">đang khám phá phim mỗi ngày</p>
                        </div>
                    </div>
                </div>

                {{-- Right: Hero Visual --}}
                <div class="hidden lg:block animate-slide-up" style="animation-delay: 0.2s;">
                    <div class="relative">
                        {{-- Floating movie cards --}}
                        <div class="relative w-full aspect-square max-w-lg mx-auto">
                            {{-- Card 1: Main --}}
                            <div
                                class="absolute top-8 left-8 right-4 card p-4 shadow-2xl shadow-dark-950/50 hover:-translate-y-1 transition-transform duration-300">
                                <div class="flex gap-4">
                                    <div
                                        class="w-24 h-36 bg-gradient-to-br from-accent-500/20 to-accent-700/20 rounded-lg flex items-center justify-center shrink-0">
                                        <span class="text-4xl">🎬</span>
                                    </div>
                                    <div class="space-y-2 min-w-0">
                                        <h3 class="font-display font-bold text-white text-lg">Inception</h3>
                                        <p class="text-dark-400 text-sm">Christopher Nolan</p>
                                        <div class="flex items-center gap-1">
                                            @for ($i = 0; $i < 5; $i++)
                                                <svg class="w-4 h-4 {{ $i < 4 ? 'text-accent-400' : 'text-dark-600' }}"
                                                    fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            @endfor
                                            <span class="text-dark-300 text-sm ml-1">8.8</span>
                                        </div>
                                        <p class="text-dark-400 text-xs">Sci-Fi • Thriller • 2010</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Card 2: AI Chat --}}
                            <div
                                class="absolute bottom-24 left-0 w-72 card p-4 shadow-2xl shadow-dark-950/50 hover:-translate-y-1 transition-transform duration-300">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-8 h-8 bg-accent-500/20 rounded-lg flex items-center justify-center">
                                        <span class="text-sm">🤖</span>
                                    </div>
                                    <span class="text-sm font-semibold text-white">Trợ lý AI</span>
                                </div>
                                <div class="space-y-2">
                                    <div class="bg-dark-700/50 rounded-lg rounded-tl-none p-3">
                                        <p class="text-sm text-dark-200">Bạn muốn xem phim nhẹ nhàng, lãng mạn phải
                                            không? Thử xem <span class="text-accent-400 font-medium">La La Land</span>
                                            nhé! 🎵</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Card 3: Quick Rating --}}
                            <div
                                class="absolute bottom-4 right-0 card p-4 shadow-2xl shadow-dark-950/50 hover:-translate-y-1 transition-transform duration-300">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-accent-500 rounded-full flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">9.2</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-white">Đánh giá nhanh</p>
                                        <p class="text-xs text-dark-400">Chấm điểm trong 1 giây</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Scroll indicator --}}
        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 animate-bounce">
            <svg class="w-6 h-6 text-dark-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
            </svg>
        </div>
    </section>

    {{-- Features Section --}}
    <section id="features" class="py-24 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-display font-bold text-white mb-4">
                    Tại sao chọn <span class="text-gradient">Reco</span>?
                </h2>
                <p class="text-dark-400 text-lg max-w-2xl mx-auto">
                    Nền tảng đánh giá phim hiện đại, tích hợp công nghệ AI để mang đến trải nghiệm cá nhân hóa
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- Feature 1 --}}
                <div class="card p-8 hover:border-accent-500/30 transition-all duration-300 group">
                    <div
                        class="w-14 h-14 bg-accent-500/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-accent-500/20 transition-colors">
                        <svg class="w-7 h-7 text-accent-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-display font-bold text-white mb-3">Đánh giá & Review</h3>
                    <p class="text-dark-400 leading-relaxed">Chấm điểm nhanh hoặc viết bài review chi tiết. Chia sẻ góc
                        nhìn của bạn về mọi bộ phim.</p>
                </div>

                {{-- Feature 2 --}}
                <div class="card p-8 hover:border-accent-500/30 transition-all duration-300 group">
                    <div
                        class="w-14 h-14 bg-accent-500/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-accent-500/20 transition-colors">
                        <svg class="w-7 h-7 text-accent-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-display font-bold text-white mb-3">AI Gợi ý thông minh</h3>
                    <p class="text-dark-400 leading-relaxed">Trò chuyện với AI để nhận gợi ý phim phù hợp với tâm trạng
                        và sở thích của bạn.</p>
                </div>

                {{-- Feature 3 --}}
                <div class="card p-8 hover:border-accent-500/30 transition-all duration-300 group">
                    <div
                        class="w-14 h-14 bg-accent-500/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-accent-500/20 transition-colors">
                        <svg class="w-7 h-7 text-accent-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-display font-bold text-white mb-3">Khám phá phim</h3>
                    <p class="text-dark-400 leading-relaxed">Tìm kiếm theo thể loại, đạo diễn, diễn viên. Dữ liệu phim
                        đồng bộ từ TMDb API.</p>
                </div>

                {{-- Feature 4 --}}
                <div class="card p-8 hover:border-accent-500/30 transition-all duration-300 group">
                    <div
                        class="w-14 h-14 bg-accent-500/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-accent-500/20 transition-colors">
                        <svg class="w-7 h-7 text-accent-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-display font-bold text-white mb-3">Watchlist cá nhân</h3>
                    <p class="text-dark-400 leading-relaxed">Lưu phim muốn xem, đang xem, đã xem. Quản lý danh sách phim
                        cá nhân của bạn.</p>
                </div>

                {{-- Feature 5 --}}
                <div class="card p-8 hover:border-accent-500/30 transition-all duration-300 group">
                    <div
                        class="w-14 h-14 bg-accent-500/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-accent-500/20 transition-colors">
                        <svg class="w-7 h-7 text-accent-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-display font-bold text-white mb-3">Cộng đồng</h3>
                    <p class="text-dark-400 leading-relaxed">Theo dõi reviewer yêu thích, bình luận, thích bài viết. Kết
                        nối với bạn bè yêu phim.</p>
                </div>

                {{-- Feature 6 --}}
                <div class="card p-8 hover:border-accent-500/30 transition-all duration-300 group">
                    <div
                        class="w-14 h-14 bg-accent-500/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-accent-500/20 transition-colors">
                        <svg class="w-7 h-7 text-accent-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-display font-bold text-white mb-3">Thông báo</h3>
                    <p class="text-dark-400 leading-relaxed">Nhận thông báo khi có ai thích, bình luận hoặc theo dõi
                        bạn. Không bỏ lỡ điều gì.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- AI Section --}}
    <section id="ai" class="py-24 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-accent-500/5 to-transparent"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                {{-- Chat Demo --}}
                <div class="card p-6 max-w-md mx-auto lg:mx-0">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-dark-700/50">
                        <div class="w-10 h-10 bg-accent-500 rounded-xl flex items-center justify-center">
                            <span class="text-white text-lg">🤖</span>
                        </div>
                        <div>
                            <p class="font-display font-bold text-white">Trợ lý AI Reco</p>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                <span class="text-xs text-dark-400">Online</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        {{-- User message --}}
                        <div class="flex justify-end">
                            <div
                                class="bg-accent-500/20 border border-accent-500/30 rounded-2xl rounded-br-md px-4 py-3 max-w-[80%]">
                                <p class="text-sm text-dark-100">Tôi đang buồn, muốn xem phim vui vẻ 😢</p>
                            </div>
                        </div>

                        {{-- AI response --}}
                        <div class="flex justify-start">
                            <div
                                class="bg-dark-700/50 border border-dark-600/50 rounded-2xl rounded-bl-md px-4 py-3 max-w-[85%]">
                                <p class="text-sm text-dark-200 leading-relaxed">Mình hiểu rồi! 💛 Đây là 3 gợi ý phim
                                    sẽ giúp bạn vui lên:</p>
                                <div class="mt-3 space-y-2">
                                    <div class="flex items-center gap-2 text-sm">
                                        <span>🎬</span>
                                        <span class="text-accent-400 font-medium">The Grand Budapest Hotel</span>
                                        <span class="text-dark-400">⭐ 8.1</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm">
                                        <span>🎬</span>
                                        <span class="text-accent-400 font-medium">Soul (Pixar)</span>
                                        <span class="text-dark-400">⭐ 8.0</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm">
                                        <span>🎬</span>
                                        <span class="text-accent-400 font-medium">The Secret Life of Walter Mitty</span>
                                        <span class="text-dark-400">⭐ 7.3</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Text --}}
                <div class="space-y-6">
                    <div
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-accent-500/10 border border-accent-500/20">
                        <span class="text-accent-400 text-sm font-medium">🤖 Powered by AI</span>
                    </div>

                    <h2 class="text-3xl sm:text-4xl font-display font-bold text-white leading-tight">
                        Trợ lý AI hiểu<br>
                        <span class="text-gradient">tâm trạng của bạn</span>
                    </h2>

                    <p class="text-dark-300 text-lg leading-relaxed">
                        Chỉ cần nói với AI bạn đang cảm thấy gì, AI sẽ phân tích và gợi ý những bộ phim phù hợp nhất —
                        ưu tiên từ kho phim trong hệ thống và TMDb.
                    </p>

                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-accent-500 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-dark-300">Gợi ý phim dựa trên tâm trạng, sở thích cá nhân</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-accent-500 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-dark-300">Trả lời câu hỏi về phim, diễn viên, đạo diễn</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-accent-500 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-dark-300">Học từ lịch sử đánh giá để cải thiện gợi ý</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="py-24">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="card p-12 sm:p-16 relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-accent-500/5 to-transparent"></div>
                <div class="relative space-y-6">
                    <h2 class="text-3xl sm:text-4xl font-display font-bold text-white">
                        Sẵn sàng khám phá?
                    </h2>
                    <p class="text-dark-300 text-lg max-w-xl mx-auto">
                        Tham gia cộng đồng yêu phim, viết review, và nhận gợi ý thông minh từ AI ngay hôm nay.
                    </p>
                    <div class="flex flex-wrap justify-center gap-4 pt-4">
                        <a href="{{ route('register') }}" class="btn-primary text-base !px-8 !py-4">
                            Đăng ký miễn phí
                        </a>
                        <a href="{{ route('login') }}" class="btn-secondary text-base !px-8 !py-4">
                            Đăng nhập
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="border-t border-dark-800 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-accent-500 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                        </svg>
                    </div>
                    <span class="text-lg font-display font-bold text-white">Reco</span>
                </div>
                <p class="text-dark-500 text-sm">
                    © {{ date('Y') }} Reco — Chuyên đề tốt nghiệp · Nguyễn Đức Thông · 64132336
                </p>
            </div>
        </div>
    </footer>
</x-base-layout>