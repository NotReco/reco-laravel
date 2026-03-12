{{-- Hero Carousel Component --}}
{{-- Usage: <x-hero-carousel :movies="$heroMovies" /> --}}

@props(['movies'])

@if($movies->isNotEmpty())
<section class="relative min-h-[600px] lg:min-h-[80vh] overflow-hidden -mt-16"
    x-data="{
        current: 0,
        total: {{ $movies->count() }},
        trailerUrl: '',
        showTrailer: false,
        autoplay: null,
        startAutoplay() {
            this.autoplay = setInterval(() => this.next(), 6000);
        },
        stopAutoplay() {
            clearInterval(this.autoplay);
        },
        next() {
            this.current = (this.current + 1) % this.total;
        },
        prev() {
            this.current = (this.current - 1 + this.total) % this.total;
        },
        openTrailer(url) {
            if (!url) return;
            const videoId = url.includes('v=') ? url.split('v=')[1].split('&')[0] : url.split('/').pop();
            this.trailerUrl = 'https://www.youtube.com/embed/' + videoId + '?autoplay=1&rel=0';
            this.showTrailer = true;
            this.stopAutoplay();
        },
        closeTrailer() {
            this.showTrailer = false;
            this.trailerUrl = '';
            this.startAutoplay();
        }
    }"
    x-init="startAutoplay()"
    @keydown.escape.window="closeTrailer()"
>

    {{-- Backdrop Slides --}}
    @foreach($movies as $i => $movie)
        <div x-show="current === {{ $i }}"
            x-transition:enter="transition ease-out duration-700"
            x-transition:enter-start="opacity-0 scale-105"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-500"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute inset-0"
        >
            @if($movie->backdrop)
                <img src="{{ $movie->backdrop }}" alt="{{ $movie->title }}" class="w-full h-full object-cover">
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-dark-950 via-dark-950/60 to-dark-950/30"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-dark-950/80 via-dark-950/30 to-transparent"></div>
        </div>
    @endforeach

    {{-- Content Layer --}}
    <div class="absolute inset-0 flex items-end pb-16 lg:pb-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <div class="flex flex-col lg:flex-row items-end justify-between gap-8">

                {{-- Left: Movie Info --}}
                <div class="flex-1 max-w-2xl space-y-4">
                    @foreach($movies as $i => $movie)
                        <div x-show="current === {{ $i }}"
                            x-transition:enter="transition ease-out duration-500 delay-200"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0"
                        >
                            {{-- Badge --}}
                            <span class="badge-rose mb-3">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"/></svg>
                                PHIM ĐIỆN ẢNH
                            </span>

                            {{-- Title --}}
                            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-display font-bold text-white leading-tight">
                                {{ $movie->title }}
                            </h1>

                            {{-- Meta --}}
                            <div class="flex items-center gap-3 flex-wrap text-sm text-dark-300 mt-3">
                                @if($movie->release_date)
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="4"/></svg>
                                        {{ $movie->release_date->format('Y') }}
                                    </span>
                                @endif
                                @if($movie->avg_rating > 0)
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        {{ number_format($movie->avg_rating, 1) }}
                                    </span>
                                @endif
                                @if($movie->rating_count > 0)
                                    <span>👥 {{ $movie->rating_count }} đánh giá</span>
                                @endif
                                @if($movie->runtime)
                                    <span>🕐 {{ $movie->runtime }} phút</span>
                                @endif
                            </div>

                            {{-- Rating Score Box --}}
                            @if($movie->avg_rating > 0)
                                <div class="flex items-center gap-3 mt-3">
                                    <div class="glass-light rounded-xl px-4 py-2 flex items-center gap-3">
                                        <span class="text-2xl font-bold text-white">{{ number_format($movie->avg_rating, 1) }}</span>
                                        <div>
                                            <x-star-rating :rating="$movie->avg_rating" :max="10" size="sm" />
                                            <p class="text-xs text-dark-400 mt-0.5">RecoDB Score</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Synopsis --}}
                            @if($movie->synopsis)
                                <p class="text-dark-300 text-sm leading-relaxed line-clamp-3 mt-3 max-w-lg">
                                    {{ $movie->synopsis }}
                                </p>
                            @endif

                            {{-- CTA Buttons --}}
                            <div class="flex items-center gap-3 mt-5">
                                @if($movie->trailer_url)
                                    <button @click="openTrailer('{{ $movie->trailer_url }}')" class="btn-rose">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/></svg>
                                        Xem trailer
                                    </button>
                                @endif
                                <a href="{{ route('movies.show', $movie) }}" class="btn-ghost">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Chi tiết
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Right: Poster Thumbnails (desktop) --}}
                <div class="hidden lg:flex items-end gap-3">
                    @foreach($movies as $i => $movie)
                        @if($movie->poster)
                            <button @click="current = {{ $i }}"
                                :class="current === {{ $i }}
                                    ? 'ring-2 ring-rose-500 scale-105 opacity-100 w-28 h-40'
                                    : 'opacity-50 hover:opacity-80 w-20 h-28'"
                                class="shrink-0 rounded-xl overflow-hidden transition-all duration-300 shadow-xl"
                            >
                                <img src="{{ $movie->poster }}" alt="{{ $movie->title }}" class="w-full h-full object-cover">
                            </button>
                        @endif
                    @endforeach
                </div>

            </div>
        </div>
    </div>

    {{-- Navigation Arrows --}}
    <button @click="prev(); stopAutoplay(); startAutoplay()"
        class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-dark-900/60 backdrop-blur-sm border border-white/10 text-white flex items-center justify-center hover:bg-white/20 transition-all z-10">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </button>
    <button @click="next(); stopAutoplay(); startAutoplay()"
        class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-dark-900/60 backdrop-blur-sm border border-white/10 text-white flex items-center justify-center hover:bg-white/20 transition-all z-10">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </button>

    {{-- Dot Indicators --}}
    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-2 z-10">
        @for($i = 0; $i < $movies->count(); $i++)
            <button @click="current = {{ $i }}; stopAutoplay(); startAutoplay()"
                :class="current === {{ $i }} ? 'bg-white w-8' : 'bg-white/40 w-2 hover:bg-white/60'"
                class="h-2 rounded-full transition-all duration-300"></button>
        @endfor
    </div>

    {{-- Trailer Modal --}}
    <x-trailer-modal />

</section>
@endif
