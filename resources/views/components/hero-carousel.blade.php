{{-- Hero Carousel Component --}}
{{-- Usage: <x-hero-carousel :movies="$heroMovies" /> --}}

@props(['items'])

@if($items->isNotEmpty())
<section class="relative min-h-[600px] lg:min-h-[80vh] overflow-hidden -mt-16"
    x-data="{
        current: 0,
        total: {{ $items->count() }},
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
        }
    }"
    x-init="startAutoplay()"
    @trailer-opened.window="stopAutoplay()"
    @trailer-closed.window="startAutoplay()"
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
            <div class="flex flex-col lg:flex-row items-start lg:items-end justify-between gap-8">

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
                                    PHIM BỘ
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


                            {{-- Synopsis --}}
                            @if($item->synopsis)
                                <p class="text-gray-700 text-sm leading-relaxed line-clamp-3 mt-3 max-w-lg">
                                    {{ $item->synopsis }}
                                </p>
                            @endif

                            <div class="flex items-center gap-3 mt-5">
                                @if($item->trailer_url)
                                    <button @click="$store.trailerModal.open('{{ $item->trailer_url }}', [])" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-sky-600 hover:bg-sky-500 transition-colors shadow-md shadow-sky-200 flex items-center">
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

</section>
@else
{{-- Fallback Hero khi không có phim carousel --}}
<section class="relative min-h-[400px] lg:min-h-[50vh] overflow-hidden -mt-16 bg-gradient-to-br from-gray-50 via-sky-50/40 to-indigo-50/60 flex items-center pt-24 pb-12 lg:pt-32 lg:pb-16">
    {{-- Background decoration --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-sky-100/60 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-indigo-100/60 rounded-full blur-3xl"></div>
        <div class="absolute top-1/3 left-1/2 -translate-x-1/2 w-[600px] h-[300px] bg-gradient-to-r from-sky-100/40 via-indigo-100/40 to-purple-100/40 rounded-full blur-2xl"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        <div class="flex flex-col lg:flex-row items-center justify-between gap-10">

            {{-- Left: Welcome Message --}}
            <div class="flex-1 max-w-2xl space-y-5 text-center lg:text-left">
                <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-sky-100 text-sky-700 text-xs font-semibold rounded-full border border-sky-200">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    Khám phá điện ảnh
                </span>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-heading font-bold text-gray-900 leading-tight">
                    Thế giới phim<br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-600 to-indigo-600">trong tầm tay</span>
                </h1>

                <p class="text-gray-600 text-base lg:text-lg leading-relaxed max-w-lg mx-auto lg:mx-0">
                    Khám phá hàng ngàn bộ phim, đọc đánh giá từ cộng đồng và tìm kiếm những tác phẩm điện ảnh phù hợp với bạn.
                </p>

                <div class="flex items-center gap-3 flex-wrap justify-center lg:justify-start pt-2">
                    <a href="{{ route('explore') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-sky-600 text-white text-sm font-semibold rounded-xl hover:bg-sky-500 transition-colors shadow-md shadow-sky-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/></svg>
                        Khám phá phim lẻ
                    </a>
                    <a href="{{ route('tv-shows.index') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-white text-gray-700 text-sm font-semibold rounded-xl border border-gray-200 hover:bg-gray-50 transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Xem phim bộ
                    </a>
                </div>
            </div>

            {{-- Right: Floating info cards --}}
            <div class="hidden lg:flex flex-col gap-3 w-64 shrink-0">
                <div class="bg-white/80 backdrop-blur-sm border border-gray-200/80 rounded-2xl p-4 shadow-sm flex items-center gap-3">
                    <div class="w-10 h-10 bg-sky-100 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Phim lẻ & phim bộ</p>
                        <p class="text-sm font-bold text-gray-900">Đa dạng thể loại</p>
                    </div>
                </div>
                <div class="bg-white/80 backdrop-blur-sm border border-gray-200/80 rounded-2xl p-4 shadow-sm flex items-center gap-3">
                    <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Đánh giá cộng đồng</p>
                        <p class="text-sm font-bold text-gray-900">Từ người thật</p>
                    </div>
                </div>
                <div class="bg-white/80 backdrop-blur-sm border border-gray-200/80 rounded-2xl p-4 shadow-sm flex items-center gap-3">
                    <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Cộng đồng</p>
                        <p class="text-sm font-bold text-gray-900">Sôi động mỗi ngày</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
@endif
