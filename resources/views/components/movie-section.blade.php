{{-- Movie Section Component --}}
{{-- Horizontal scroll movie section with header --}}
{{-- Usage: <x-movie-section title="🔥 Trending" :items="$movies" :seeAllUrl="route('explore', ['sort' => 'popular'])" /> --}}

@props(['title', 'subtitle' => '', 'items', 'seeAllUrl' => null, 'layout' => 'scroll'])

@if($items->isNotEmpty())
<section>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-2">
        <h2 class="section-title">{{ $title }}</h2>
        @if($seeAllUrl)
            <a href="{{ $seeAllUrl }}" class="section-link">Xem tất cả →</a>
        @endif
    </div>
    @if($subtitle)
        <p class="section-subtitle">{{ $subtitle }}</p>
    @endif

    @if($layout === 'scroll')
        {{-- Horizontal Scroll Layout --}}
        <div class="flex gap-4 overflow-x-auto pb-4 scrollbar-hide snap-x snap-mandatory">
            @foreach($items as $i => $movie)
                <x-movie-card :movie="$movie" :rank="$i + 1" class="shrink-0 w-44 snap-start" />
            @endforeach
        </div>
    @else
        {{-- Grid Layout --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            @foreach($items as $movie)
                <x-movie-card :movie="$movie" />
            @endforeach
        </div>
    @endif
</section>
@endif
