{{-- Review Card Component --}}
{{-- Usage: <x-review-card :review="$review" /> --}}

@props(['review', 'showMovie' => true])

@php
    $ratingColor = match(true) {
        $review->rating >= 9 => 'rating-bg-excellent',
        $review->rating >= 7 => 'rating-bg-good',
        $review->rating >= 5 => 'rating-bg-average',
        default => 'rating-bg-terrible',
    };

    $ratingLabel = match(true) {
        $review->rating >= 9 => 'Kiệt tác',
        $review->rating >= 8 => 'Xuất sắc',
        $review->rating >= 7 => 'Rất tốt',
        $review->rating >= 6 => 'Tốt',
        $review->rating >= 5 => 'Khá',
        $review->rating >= 4 => 'Trung bình',
        $review->rating >= 3 => 'Tạm được',
        $review->rating >= 2 => 'Tệ',
        default => 'Rất tệ',
    };
@endphp

<div {{ $attributes->merge(['class' => 'card-hover p-5']) }}>
    {{-- Header: User + Rating --}}
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-3 min-w-0">
            {{-- Avatar --}}
            <div class="w-10 h-10 rounded-full bg-dark-600 flex items-center justify-center overflow-hidden shrink-0 ring-2 ring-dark-700">
                @if($review->user?->avatar)
                    <img src="{{ $review->user->avatar }}" class="w-full h-full object-cover" alt="">
                @else
                    <span class="text-sm font-bold text-dark-300">{{ strtoupper(substr($review->user?->name ?? '?', 0, 1)) }}</span>
                @endif
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-white truncate">{{ $review->user?->name ?? 'Ẩn danh' }}</p>
                <p class="text-xs text-dark-400">{{ $review->published_at?->diffForHumans() }}</p>
            </div>
        </div>

        {{-- Rating Badge --}}
        @if($review->rating)
            <div class="flex items-center gap-1.5 shrink-0">
                <div class="{{ $ratingColor }} px-2.5 py-1 rounded-lg">
                    <span class="text-white text-sm font-bold">{{ number_format($review->rating, 1) }}</span>
                </div>
            </div>
        @endif
    </div>

    {{-- Movie link --}}
    @if($showMovie && $review->movie)
        <a href="{{ route('movies.show', $review->movie) }}"
            class="flex items-center gap-2 text-xs text-rose-400 hover:text-rose-300 transition-colors mb-2">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/></svg>
            {{ $review->movie->title }}
        </a>
    @endif

    {{-- Review title --}}
    @if($review->title)
        <h4 class="font-semibold text-white text-sm mb-1 group-hover:text-rose-400 transition-colors">{{ $review->title }}</h4>
    @endif

    {{-- Review content --}}
    @if($review->is_spoiler)
        <x-spoiler-toggle>
            <p class="text-dark-400 text-sm leading-relaxed line-clamp-3">
                {{ $review->excerpt ?? Str::limit($review->content, 150) }}
            </p>
        </x-spoiler-toggle>
    @else
        <p class="text-dark-400 text-sm leading-relaxed line-clamp-3">
            {{ $review->excerpt ?? Str::limit($review->content, 150) }}
        </p>
    @endif

    {{-- Rating label --}}
    @if($review->rating)
        <p class="text-xs mt-2 {{ str_replace('rating-bg-', 'rating-', $ratingColor) }}">{{ $ratingLabel }}</p>
    @endif
</div>
