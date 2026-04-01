{{-- Movie Section Component --}}
{{-- Usage: <x-movie-section title="🔥 Trending" :items="$movies" /> --}}

@props(['title', 'subtitle' => '', 'items', 'layout' => 'scroll', 'showRank' => false])

<section>
    {{-- TMDB-style Section Header: left accent bar + title --}}
    <div class="mb-5">
        <div class="flex items-center gap-3">
            {{-- Accent bar (TMDB-style) --}}
            <div class="w-1 h-9 bg-rose-500 rounded-full shrink-0"></div>
            <div>
                <h2 class="text-xl lg:text-2xl font-heading font-bold text-gray-900 leading-tight">
                    {{ $title }}</h2>
                @if ($subtitle)
                    <p class="text-gray-500 text-sm mt-0.5">{{ $subtitle }}</p>
                @endif
            </div>
        </div>
    </div>

    @if ($items->isNotEmpty())
        @if ($layout === 'scroll')
            {{-- Carousel Grid Layout: slides left/right like a real carousel --}}
            @php
                $perPage = 6;
                $totalItems = $items->count();
                $totalPages = (int) ceil($totalItems / $perPage);
            @endphp

            <div x-data="{
                currentPage: 0,
                totalPages: {{ $totalPages }},
                get canGoPrev() { return this.currentPage > 0; },
                get canGoNext() { return this.currentPage < this.totalPages - 1; },
                get offset() { return -(this.currentPage * 100) + '%'; },
                goNext() {
                    if (this.canGoNext) this.currentPage++;
                },
                goPrev() {
                    if (this.canGoPrev) this.currentPage--;
                },
            }" class="relative group/slider">

                {{-- Carousel track: overflow hidden clips the content --}}
                <div class="overflow-hidden -mx-2">
                    <div class="flex transition-transform duration-[600ms] ease-[cubic-bezier(0.25,0.1,0.25,1)]"
                        :style="'transform: translateX(' + offset + ')'">

                        {{-- Each item takes exactly 1/6 of the container width --}}
                        @foreach ($items as $i => $movie)
                            <div class="w-1/2 sm:w-1/3 md:w-1/4 lg:w-[calc(100%/6)] shrink-0 px-2">
                                <x-movie-card :movie="$movie" :rank="$showRank ? $i + 1 : null" :hideOriginalTitle="true" />
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Navigation Buttons --}}
                @if ($totalPages > 1)
                    {{-- Nút trái --}}
                    <button x-show="canGoPrev" @click="goPrev()"
                        class="absolute left-0 top-[40%] -translate-y-1/2 -translate-x-1/2 z-10 w-11 h-11 border-2 border-gray-600 bg-white text-gray-700 rounded-full flex items-center justify-center opacity-0 group-hover/slider:opacity-100 hover:text-rose-600 transition-all duration-300 shadow-lg hidden sm:flex">
                        <svg class="w-6 h-6 pr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>

                    {{-- Nút phải --}}
                    <button x-show="canGoNext" @click="goNext()"
                        class="absolute right-0 top-[40%] -translate-y-1/2 translate-x-1/2 z-10 w-11 h-11 border-2 border-gray-600 bg-white text-gray-700 rounded-full flex items-center justify-center opacity-0 group-hover/slider:opacity-100 hover:text-rose-600 transition-all duration-300 shadow-lg hidden sm:flex">
                        <svg class="w-6 h-6 pl-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    {{-- Page Indicator --}}
                    <div class="flex justify-center gap-1.5 mt-4">
                        @for ($p = 0; $p < $totalPages; $p++)
                            <button @click="currentPage = {{ $p }}"
                                :class="currentPage === {{ $p }} ?
                                    'bg-rose-500 w-6' :
                                    'bg-gray-300 hover:bg-gray-400 w-2'"
                                class="h-2 rounded-full transition-all duration-300">
                            </button>
                        @endfor
                    </div>
                @endif
            </div>
        @else
            {{-- Grid Layout (no pagination) --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                @foreach ($items as $movie)
                    <x-movie-card :movie="$movie" :hideOriginalTitle="true" />
                @endforeach
            </div>
        @endif
    @else
        {{-- Empty State --}}
        <div class="flex flex-col items-center justify-center py-12 text-center">
            <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
            </svg>
            <p class="text-sm font-medium text-gray-600">CHƯA CÓ PHIM NÀO TRONG MỤC NÀY</p>
            <p class="text-xs text-gray-500 mt-1">Nội dung sẽ được cập nhật sớm!</p>
        </div>
    @endif
</section>
