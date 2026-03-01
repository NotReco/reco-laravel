@props(['movie'])

<a href="{{ route('movies.show', $movie) }}" class="group block">
    <div class="card overflow-hidden hover:border-accent-500/30 transition-all duration-300 hover:-translate-y-1">
        {{-- Poster --}}
        <div class="relative aspect-[2/3] overflow-hidden bg-dark-800">
            @if($movie->poster)
                <img src="{{ $movie->poster }}" alt="{{ $movie->title }}"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                    loading="lazy">
            @else
                <div class="w-full h-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-dark-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                    </svg>
                </div>
            @endif

            {{-- Rating badge --}}
            @php
                $avgRating = $movie->reviews()->whereNotNull('rating')->avg('rating');
            @endphp
            @if($avgRating)
                <div
                    class="absolute top-2 right-2 bg-dark-950/80 backdrop-blur-sm px-2 py-1 rounded-lg flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-accent-400" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <span class="text-white text-xs font-bold">{{ number_format($avgRating, 1) }}</span>
                </div>
            @endif
        </div>

        {{-- Info --}}
        <div class="p-3 space-y-1">
            <h3
                class="font-display font-semibold text-white text-sm line-clamp-1 group-hover:text-accent-400 transition-colors">
                {{ $movie->title }}</h3>
            <div class="flex items-center gap-2 text-xs text-dark-400">
                @if($movie->release_date)
                    <span>{{ \Carbon\Carbon::parse($movie->release_date)->format('Y') }}</span>
                @endif
                @if($movie->genres->isNotEmpty())
                    <span>•</span>
                    <span class="line-clamp-1">{{ $movie->genres->take(2)->pluck('name')->join(', ') }}</span>
                @endif
            </div>
        </div>
    </div>
</a>