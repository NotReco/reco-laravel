<x-app-layout>
    <x-slot:title>Trang chủ</x-slot:title>

    {{-- Hero / Carousel --}}
    @if($latestMovies->isNotEmpty())
        <section class="relative h-[500px] overflow-hidden" x-data="{ current: 0, total: {{ $latestMovies->count() }} }"
            x-init="setInterval(() => current = (current + 1) % total, 5000)">
            @foreach($latestMovies as $i => $movie)
                <div x-show="current === {{ $i }}" x-transition:enter="transition ease-out duration-700"
                    x-transition:enter-start="opacity-0 scale-105" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" class="absolute inset-0">
                    @if($movie->backdrop)
                        <img src="{{ $movie->backdrop }}" alt="{{ $movie->title }}" class="w-full h-full object-cover">
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-dark-950 via-dark-950/60 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-8 md:p-16 max-w-7xl mx-auto">
                        <h2 class="text-3xl md:text-4xl font-display font-bold text-white mb-2">{{ $movie->title }}</h2>
                        <p class="text-dark-300 max-w-xl line-clamp-2 mb-4">{{ $movie->synopsis }}</p>
                        <div class="flex items-center gap-4">
                            <a href="{{ route('movies.show', $movie) }}" class="btn-primary !py-2.5">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Xem chi tiết
                            </a>
                            @if($movie->release_date)
                                <span
                                    class="text-dark-400 text-sm">{{ \Carbon\Carbon::parse($movie->release_date)->format('Y') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Dots --}}
            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 z-10">
                @for($i = 0; $i < $latestMovies->count(); $i++)
                    <button @click="current = {{ $i }}" :class="current === {{ $i }} ? 'bg-accent-500 w-6' : 'bg-dark-500 w-2'"
                        class="h-2 rounded-full transition-all duration-300"></button>
                @endfor
            </div>
        </section>
    @endif

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-16">

        {{-- Thể loại --}}
        @if($genres->isNotEmpty())
            <section>
                <div class="flex items-center gap-3 flex-wrap">
                    <a href="{{ route('movies.index') }}"
                        class="px-4 py-2 rounded-full text-sm font-medium bg-accent-500 text-white">Tất cả</a>
                    @foreach($genres as $genre)
                        <a href="{{ route('movies.index', ['genre' => $genre->id]) }}"
                            class="px-4 py-2 rounded-full text-sm font-medium bg-dark-800 text-dark-300 border border-dark-700 hover:border-accent-500/50 hover:text-white transition-colors">
                            {{ $genre->name }}
                            <span class="text-dark-500 ml-1">{{ $genre->movies_count }}</span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Phim đánh giá cao --}}
        @if($topRatedMovies->isNotEmpty())
            <section>
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-display font-bold text-white">⭐ Đánh giá cao nhất</h2>
                    <a href="{{ route('movies.index', ['sort' => 'top_rated']) }}"
                        class="text-accent-400 hover:text-accent-300 text-sm font-medium transition-colors">Xem tất cả →</a>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach($topRatedMovies as $movie)
                        <x-movie-card :movie="$movie" />
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Phim phổ biến --}}
        @if($popularMovies->isNotEmpty())
            <section>
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-display font-bold text-white">🔥 Phổ biến nhất</h2>
                    <a href="{{ route('movies.index', ['sort' => 'popular']) }}"
                        class="text-accent-400 hover:text-accent-300 text-sm font-medium transition-colors">Xem tất cả →</a>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach($popularMovies as $movie)
                        <x-movie-card :movie="$movie" />
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Review mới nhất --}}
        @if($latestReviews->isNotEmpty())
            <section>
                <h2 class="text-2xl font-display font-bold text-white mb-6">📝 Review mới nhất</h2>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($latestReviews as $review)
                        <div class="card p-5 hover:border-accent-500/30 transition-colors">
                            <div class="flex items-center gap-3 mb-3">
                                <div
                                    class="w-8 h-8 rounded-full bg-dark-600 flex items-center justify-center overflow-hidden shrink-0">
                                    @if($review->user->avatar)
                                        <img src="{{ $review->user->avatar }}" class="w-full h-full object-cover">
                                    @else
                                        <span
                                            class="text-xs font-bold text-dark-300">{{ strtoupper(substr($review->user->name, 0, 1)) }}</span>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-white truncate">{{ $review->user->name }}</p>
                                    <p class="text-xs text-dark-400">{{ $review->published_at->diffForHumans() }}</p>
                                </div>
                                @if($review->rating)
                                    <div class="ml-auto flex items-center gap-1 shrink-0">
                                        <svg class="w-4 h-4 text-accent-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        <span class="text-sm font-bold text-white">{{ number_format($review->rating, 1) }}</span>
                                    </div>
                                @endif
                            </div>
                            @if($review->movie)
                                <a href="{{ route('movies.show', $review->movie) }}"
                                    class="text-xs text-accent-400 hover:text-accent-300 transition-colors mb-2 block">🎬
                                    {{ $review->movie->title }}</a>
                            @endif
                            @if($review->title)
                                <h3 class="font-semibold text-white text-sm mb-1">{{ $review->title }}</h3>
                            @endif
                            <p class="text-dark-400 text-sm line-clamp-3">
                                {{ $review->excerpt ?? Str::limit($review->content, 120) }}</p>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

    </div>
</x-app-layout>