{{-- Hero Carousel Component --}}
{{-- Usage: <x-hero-carousel :movies="$heroMovies" /> --}}

@props(['items'])

@if($items->isNotEmpty())
<section class="relative min-h-[600px] lg:min-h-[80vh] overflow-hidden -mt-16"
    x-data="{
        current: 0,
        total: {{ $items->count() }},
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
    @foreach($items as $i => $item)
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
            @if($item->backdrop)
                <img src="{{ $item->backdrop }}" alt="{{ $item->title }}" 
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
                    @foreach($items as $i => $item)
                        <div {{ $i > 0 ? 'x-cloak style=display:none' : '' }}
                            x-show="current === {{ $i }}"
                            x-transition:enter="transition ease-out duration-500 delay-200"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0"
                        >
                            {{-- Badge --}}
                            @if(get_class($item) === 'App\Models\Movie')
                                <span class="badge-sky mb-3">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"/></svg>
                                    PHIM ĐIỆN ẢNH
                                </span>
                            @else
                                <span class="badge-sky mb-3 bg-indigo-50 text-indigo-700 border-indigo-200">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M2.695 14.763l-1.262 3.155a.5.5 0 00.65.65l3.155-1.262a4 4 0 001.343-3.084V14a.5.5 0 011 0v.222a5 5 0 01-1.678 3.854l-3.95 2.112a1.5 1.5 0 01-2.091-2.09l2.112-3.951A5 5 0 015.778 13H6a.5.5 0 010 1h-.222a4 4 0 00-3.083 1.343v-.58zm7.305 1.487v-.55a4.004 4.004 0 00-3.083-1.343H6a.5.5 0 010-1h.917A5.002 5.002 0 0110 12.012v.238a.5.5 0 01-1 0v-.238a4 4 0 00-1.343-3.084V6a.5.5 0 011 0v.222a5 5 0 011.678-3.854l3.95-2.112a1.5 1.5 0 012.09 2.09l-2.112 3.951A5 5 0 0114.222 13H14a.5.5 0 010-1h.222a4 4 0 003.083-1.343v.58zm-7.305-1.487"/></svg>
                                    TV SERIES
                                </span>
                            @endif

                            {{-- Title --}}
                            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-heading font-bold text-gray-900 leading-tight">
                                {{ $item->title }}
                            </h1>

                            {{-- Meta --}}
                            <div class="flex items-center gap-3 flex-wrap text-sm text-gray-600 mt-3">
                                @if($item->release_date)
                                    <span class="flex items-center gap-1 font-medium bg-gray-100 px-2.5 py-1 rounded-md border border-gray-200">
                                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="4"/></svg>
                                        {{ $item->release_date->format('Y') }}
                                    </span>
                                @elseif($item->first_air_date)
                                    <span class="flex items-center gap-1 font-medium bg-gray-100 px-2.5 py-1 rounded-md border border-gray-200">
                                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="4"/></svg>
                                        {{ $item->first_air_date->format('Y') }}
                                    </span>
                                @endif
                                @if($item->avg_rating > 0)
                                    <span class="flex items-center gap-1 font-medium bg-gray-100 px-2.5 py-1 rounded-md border border-gray-200">
                                        <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        {{ number_format($item->avg_rating, 1) }}
                                    </span>
                                @endif
                                @if($item->rating_count > 0)
                                    <span>👥 {{ $item->rating_count }} đánh giá</span>
                                @endif
                                @if($item->runtime)
                                    <span>🕐 {{ $item->runtime }} phút</span>
                                @elseif($item->number_of_seasons)
                                    <span>📺 {{ $item->number_of_seasons }} mùa</span>
                                @endif
                            </div>

                            {{-- Rating Score Box --}}
                            @if($item->avg_rating > 0)
                                <div class="flex items-center gap-3 mt-3">
                                    <div class="bg-white/80 backdrop-blur border border-gray-200 shadow-sm rounded-xl px-4 py-2 flex items-center gap-3">
                                        <span class="text-2xl font-bold text-gray-900">{{ number_format($item->avg_rating, 1) }}</span>
                                        <div>
                                            <x-star-rating :rating="$item->avg_rating" :max="10" size="sm" />
                                            <p class="text-xs text-gray-500 font-medium mt-0.5">RecoDB Score</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Synopsis --}}
                            @if($item->synopsis)
                                <p class="text-gray-700 text-sm leading-relaxed line-clamp-3 mt-3 max-w-lg">
                                    {{ $item->synopsis }}
                                </p>
                            @endif

                            {{-- CTA Buttons --}}
                            <div class="flex items-center gap-3 mt-5">
                                @if($item->trailer_url)
                                    <button @click="openTrailer('{{ $item->trailer_url }}')" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-sky-600 hover:bg-sky-500 transition-colors shadow-md shadow-sky-200 flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/></svg>
                                        Xem trailer
                                    </button>
                                @endif
                                
                                @php
                                    $routeInfo = get_class($item) === 'App\Models\Movie' ? route('movies.show', $item) : route('tv-shows.show', $item);
                                @endphp
                                <a href="{{ $routeInfo }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 transition-colors flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Chi tiết
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Right: Poster Thumbnails (desktop) --}}
                <div class="hidden lg:block relative z-10 w-[508px] h-44 overflow-hidden py-2 pl-2" x-cloak>
                    @foreach($items as $i => $item)
                        @if($item->poster)
                            @php $total = $items->count(); @endphp
                            <button @click="current = {{ $i }}; stopAutoplay(); startAutoplay()"
                                x-data="{
                                    get pos() {
                                        let p = ({{ $i }} - current + {{ $total }}) % {{ $total }};
                                        return p > 10 ? p - {{ $total }} : p;
                                    },
                                    get isVisible() {
                                        return this.pos >= -1 && this.pos <= 5;
                                    },
                                    get leftPos() {
                                        if (this.pos === 0) return 8;
                                        if (this.pos > 0) return 8 + 112 + 16 + (this.pos - 1) * 96;
                                        return 8 + this.pos * 96;
                                    }
                                }"
                                class="absolute top-[8px] rounded-xl overflow-hidden shadow-xl bg-gray-100"
                                :class="{
                                    'ring-4 ring-sky-500 ring-offset-4 ring-offset-white opacity-100 w-28 h-40 z-30': pos === 0,
                                    'opacity-70 hover:opacity-100 w-20 h-28 mt-12 z-20': pos !== 0,
                                    'transition-all duration-500 ease-in-out': isVisible,
                                }"
                                :style="`left: ${leftPos}px; ${!isVisible ? 'opacity: 0; pointer-events: none; visibility: hidden; transition: none;' : ''}`"
                            >
                                <img src="{{ $item->poster }}" alt="{{ $item->title }}" class="w-full h-full object-cover bg-gray-200">
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
        @for($i = 0; $i < $items->count(); $i++)
            <button @click="current = {{ $i }}; stopAutoplay(); startAutoplay()"
                class="h-2 rounded-full transition-all duration-300 {{ $i === 0 ? 'bg-sky-500 w-8' : 'bg-gray-300 w-2 hover:bg-gray-400' }}"
                :class="{ 'bg-sky-500 w-8': current === {{ $i }}, 'bg-gray-300 w-2 hover:bg-gray-400': current !== {{ $i }} }"></button>
        @endfor
    </div>

    {{-- Trailer Modal --}}
    <x-trailer-modal />

</section>
@endif
