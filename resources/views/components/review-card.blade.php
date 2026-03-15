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
            <a href="{{ route('profile.show', $review->user->id) }}" class="w-10 h-10 rounded-full bg-dark-600 flex items-center justify-center overflow-hidden shrink-0 ring-2 ring-dark-700 hover:ring-rose-500 transition-colors">
                @if($review->user?->avatar)
                    <img src="{{ $review->user->avatar }}" class="w-full h-full object-cover" alt="">
                @else
                    <span class="text-sm font-bold text-dark-300">{{ strtoupper(substr($review->user?->name ?? '?', 0, 1)) }}</span>
                @endif
            </a>
            <div class="min-w-0">
                <a href="{{ route('profile.show', $review->user->id) }}" class="block text-sm font-semibold text-white truncate hover:text-rose-400 transition-colors">{{ $review->user?->name ?? 'Ẩn danh' }}</a>
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
        <p class="text-xs mt-2 mb-4 {{ str_replace('rating-bg-', 'rating-', $ratingColor) }}">{{ $ratingLabel }}</p>
    @endif

    {{-- Interactions (Likes & Comments) --}}
    <div x-data="{
        liked: {{ auth()->check() && $review->likes->contains('user_id', auth()->id()) ? 'true' : 'false' }},
        likesCount: {{ $review->likes_count }},
        showComments: false,
        async toggleLike() {
            @guest
                window.location.href = '{{ route('login') }}';
                return;
            @endguest
            
            try {
                const res = await fetch('{{ route('likes.toggle') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ type: 'review', id: {{ $review->id }} })
                });
                const data = await res.json();
                if (data.success) {
                    this.liked = data.is_liked;
                    this.likesCount = data.likes_count;
                }
            } catch (error) {
                console.error('Error toggling like:', error);
            }
        }
    }" class="mt-4 pt-4 border-t border-dark-700/50">
        <div class="flex items-center gap-4">
            {{-- Like Button --}}
            <button @click="toggleLike()" class="flex items-center gap-1.5 text-sm transition-colors group"
                :class="liked ? 'text-rose-500' : 'text-dark-400 hover:text-white'">
                <svg class="w-4 h-4 transition-transform group-active:scale-75" :fill="liked ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                <span x-text="likesCount > 0 ? likesCount : 'Thích'"></span>
            </button>

            {{-- Comment Toggle --}}
            <button @click="showComments = !showComments" class="flex items-center gap-1.5 text-sm text-dark-400 hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <span>{{ $review->comments->count() > 0 ? $review->comments->count() : 'Bình luận' }}</span>
            </button>
        </div>

        {{-- Expanded Comments Section --}}
        <div x-show="showComments" x-collapse class="mt-4 pt-4 border-t border-dark-700/50" style="display: none;">
            {{-- List existing comments --}}
            @if($review->comments->isNotEmpty())
                <div class="space-y-4 mb-4">
                    @foreach($review->comments as $comment)
                        <div class="flex gap-3">
                            <div class="w-8 h-8 rounded-full bg-dark-600 shrink-0 overflow-hidden text-center leading-8 text-xs font-bold text-dark-300">
                                @if($comment->user->avatar)
                                    <img src="{{ $comment->user->avatar }}" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                @endif
                            </div>
                            <div class="flex-1 bg-dark-800/50 rounded-lg p-3">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-bold text-white">{{ $comment->user->name }}</span>
                                    <span class="text-[10px] text-dark-400">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-sm text-dark-300">{{ $comment->content }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Comment form --}}
            @auth
                <form action="{{ route('comments.store') }}" method="POST" class="flex gap-3">
                    @csrf
                    <input type="hidden" name="review_id" value="{{ $review->id }}">
                    <div class="w-8 h-8 rounded-full bg-dark-600 shrink-0 overflow-hidden text-center leading-8 text-xs font-bold text-dark-300">
                        @if(Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar }}" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        @endif
                    </div>
                    <div class="flex-1 flex gap-2">
                        <input type="text" name="content" required placeholder="Viết bình luận..." class="input-dark py-1.5 px-3 text-sm flex-1">
                        <button type="submit" class="shrink-0 px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-500 text-white text-sm font-medium transition-colors">Gửi</button>
                    </div>
                </form>
            @else
                <p class="text-xs text-center text-dark-400">Vui lòng <a href="{{ route('login') }}" class="text-rose-400 hover:underline">đăng nhập</a> để bình luận.</p>
            @endauth
        </div>
    </div>
</div>
