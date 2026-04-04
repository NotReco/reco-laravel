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
        <div {{ $i > 0 ? 'x-cloak style=display:none' : '' }}
            x-show="current === {{ $i }}"
            x-transition:enter="transition ease-out duration-700"
            x-transition:enter-start="opacity-0 scale-105"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-500"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute inset-0"
        >
            @if($movie->backdrop)
                <img src="{{ $movie->backdrop }}" alt="{{ $movie->title }}" 
                     class="w-full h-full object-cover bg-gray-200">
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-white via-white/80 to-white/30"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-white/90 via-white/50 to-transparent"></div>
        </div>
    @endforeach

    {{-- Content Layer --}}
    <div class="absolute inset-0 flex items-end pb-16 lg:pb-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <div class="flex flex-col lg:flex-row items-end justify-between gap-8">

                {{-- Left: Movie Info --}}
                <div class="flex-1 max-w-2xl space-y-4">
                    @foreach($movies as $i => $movie)
                        <div {{ $i > 0 ? 'x-cloak style=display:none' : '' }}
                            x-show="current === {{ $i }}"
                            x-transition:enter="transition ease-out duration-500 delay-200"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0"
                        >
                            {{-- Badge --}}
                            <span class="badge-sky mb-3">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"/></svg>
                                PHIM ĐIỆN ẢNH
                            </span>

                            {{-- Title --}}
                            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-heading font-bold text-gray-900 leading-tight">
                                {{ $movie->title }}
                            </h1>

                            {{-- Meta --}}
                            <div class="flex items-center gap-3 flex-wrap text-sm text-gray-600 mt-3">
                                @if($movie->release_date)
                                    <span class="flex items-center gap-1 font-medium bg-gray-100 px-2.5 py-1 rounded-md border border-gray-200">
                                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="4"/></svg>
                                        {{ $movie->release_date->format('Y') }}
                                    </span>
                                @endif
                                @if($movie->avg_rating > 0)
                                    <span class="flex items-center gap-1 font-medium bg-gray-100 px-2.5 py-1 rounded-md border border-gray-200">
                                        <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
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
                                    <div class="bg-white/80 backdrop-blur border border-gray-200 shadow-sm rounded-xl px-4 py-2 flex items-center gap-3">
                                        <span class="text-2xl font-bold text-gray-900">{{ number_format($movie->avg_rating, 1) }}</span>
                                        <div>
                                            <x-star-rating :rating="$movie->avg_rating" :max="10" size="sm" />
                                            <p class="text-xs text-gray-500 font-medium mt-0.5">RecoDB Score</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Synopsis --}}
                            @if($movie->synopsis)
                                <p class="text-gray-700 text-sm leading-relaxed line-clamp-3 mt-3 max-w-lg">
                                    {{ $movie->synopsis }}
                                </p>
                            @endif

                            {{-- CTA Buttons --}}
                            <div class="flex items-center gap-3 mt-5">
                                @if($movie->trailer_url)
                                    <button @click="openTrailer('{{ $movie->trailer_url }}')" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-sky-600 hover:bg-sky-500 transition-colors shadow-md shadow-sky-200 flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/></svg>
                                        Xem trailer
                                    </button>
                                @endif
                                <a href="{{ route('movies.show', $movie) }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 transition-colors flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Chi tiết
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Right: Poster Thumbnails (desktop) --}}
                <div class="hidden lg:flex items-end gap-3 z-10">
                    @foreach($movies as $i => $movie)
                        @if($movie->poster)
                            <button @click="current = {{ $i }}"
                                class="shrink-0 rounded-xl overflow-hidden transition-all duration-300 shadow-xl bg-gray-100 {{ $i === 0 ? 'ring-4 ring-sky-500 ring-offset-4 ring-offset-white scale-105 opacity-100 w-28 h-40' : 'opacity-70 hover:opacity-100 w-20 h-28' }}"
                                :class="{
                                    'ring-4 ring-sky-500 ring-offset-4 ring-offset-white scale-105 opacity-100 w-28 h-40': current === {{ $i }},
                                    'opacity-70 hover:opacity-100 w-20 h-28': current !== {{ $i }}
                                }"
                            >
                                <img src="{{ $movie->poster }}" alt="{{ $movie->title }}" 
                                     class="w-full h-full object-cover bg-gray-200">
                            </button>
                        @endif
                    @endforeach
                </div>

            </div>
        </div>
    </div>

    {{-- Navigation Arrows --}}
    <button @click="prev(); stopAutoplay(); startAutoplay()"
        class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/80 backdrop-blur-sm border border-gray-200 text-gray-900 shadow-md flex items-center justify-center hover:bg-white transition-all z-10">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </button>
    <button @click="next(); stopAutoplay(); startAutoplay()"
        class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/80 backdrop-blur-sm border border-gray-200 text-gray-900 shadow-md flex items-center justify-center hover:bg-white transition-all z-10">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </button>

    {{-- Dot Indicators --}}
    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-2 z-10">
        @for($i = 0; $i < $movies->count(); $i++)
            <button @click="current = {{ $i }}; stopAutoplay(); startAutoplay()"
                class="h-2 rounded-full transition-all duration-300 {{ $i === 0 ? 'bg-sky-500 w-8' : 'bg-gray-300 w-2 hover:bg-gray-400' }}"
                :class="{ 'bg-sky-500 w-8': current === {{ $i }}, 'bg-gray-300 w-2 hover:bg-gray-400': current !== {{ $i }} }"></button>
        @endfor
    </div>

    {{-- Trailer Modal --}}
    <x-trailer-modal />

</section>
@endif
