{{-- Movie Card Component --}}
{{-- Usage: <x-movie-card :movie="$movie" :rank="1" /> --}}

@props(['movie', 'rank' => null, 'showGenre' => false])

<a href="{{ route('movies.show', $movie) }}" {{ $attributes->merge(['class' => 'group block']) }}>
    {{-- Poster --}}
    <div class="relative aspect-[2/3] rounded-xl overflow-hidden bg-gray-100 shadow-sm border border-gray-200">
        @if($movie->poster)
            <img src="{{ $movie->poster }}" alt="{{ $movie->title }}"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                loading="lazy">
        @else
            <div class="w-full h-full flex items-center justify-center text-gray-400">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/></svg>
            </div>
        @endif

        {{-- Rank badge (top-left) --}}
        @if($rank)
            <div class="absolute top-2 left-2 bg-gray-900/90 backdrop-blur-sm shadow flex items-center justify-center w-7 h-7 rounded-lg">
                <span class="text-white text-xs font-bold">#{{ $rank }}</span>
            </div>
        @endif

        {{-- Rating badge (top-right) --}}
        @if($movie->avg_rating > 0)
            <div class="absolute top-2 right-2 badge-dark shadow-sm">
                <svg class="w-3.5 h-3.5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                <span>{{ number_format($movie->avg_rating, 1) }}</span>
            </div>
        @endif

        {{-- Hover overlay --}}
        <div class="absolute inset-0 bg-gradient-to-t from-gray-900/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-3">
            <span class="text-white text-xs font-medium">Xem chi tiết →</span>
        </div>
    </div>

    {{-- Title --}}
    <h3 class="mt-2 text-sm font-bold text-gray-900 line-clamp-1 group-hover:text-rose-600 transition-colors font-heading">{{ $movie->title }}</h3>

    {{-- Meta --}}
    <div class="flex items-center gap-2 text-xs text-gray-500 font-medium">
        @if($movie->release_date)
            <span>{{ $movie->release_date->format('Y') }}</span>
        @endif
        @if($showGenre && $movie->genres && $movie->genres->isNotEmpty())
            <span>•</span>
            <span class="line-clamp-1">{{ $movie->genres->take(2)->pluck('name')->join(', ') }}</span>
        @endif
    </div>
</a>