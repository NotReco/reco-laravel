@props([])

<footer class="border-t border-dark-800 py-12 mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid sm:grid-cols-3 gap-8 mb-8">
            {{-- Brand --}}
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 bg-accent-500 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                        </svg>
                    </div>
                    <span class="text-lg font-display font-bold text-white">Reco</span>
                </div>
                <p class="text-dark-400 text-sm">Cổng thông tin đánh giá phim điện ảnh tích hợp trợ lý AI gợi ý thông
                    minh.</p>
            </div>

            {{-- Links --}}
            <div>
                <h4 class="font-display font-semibold text-white mb-4">Khám phá</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('movies.index') }}"
                            class="text-dark-400 hover:text-accent-400 transition-colors">Phim</a></li>
                    <li><a href="{{ route('movies.index', ['sort' => 'top_rated']) }}"
                            class="text-dark-400 hover:text-accent-400 transition-colors">Phim đánh giá cao</a></li>
                    <li><a href="{{ route('movies.index', ['sort' => 'latest']) }}"
                            class="text-dark-400 hover:text-accent-400 transition-colors">Phim mới nhất</a></li>
                </ul>
            </div>

            {{-- Info --}}
            <div>
                <h4 class="font-display font-semibold text-white mb-4">Thông tin</h4>
                <ul class="space-y-2 text-sm">
                    <li><span class="text-dark-400">Dữ liệu phim từ TMDb API</span></li>
                    <li><span class="text-dark-400">Laravel 12 • Tailwind CSS</span></li>
                </ul>
            </div>
        </div>

        <div class="pt-8 border-t border-dark-800 text-center">
            <p class="text-dark-500 text-sm">© {{ date('Y') }} Reco — Chuyên đề tốt nghiệp · Nguyễn Đức Thông · 64132336
            </p>
        </div>
    </div>
</footer>