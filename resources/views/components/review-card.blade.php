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

<div {{ $attributes->merge(['class' => 'bg-white border border-gray-100 hover:border-gray-200 hover:shadow-lg transition-all duration-200 rounded-2xl p-5']) }}>
    {{-- Header: User + Rating --}}
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-3 min-w-0">
            <div class="relative group w-10 h-10 shrink-0">
                <div class="w-full h-full rounded-full bg-gray-100 flex items-center justify-center overflow-hidden transition-all duration-300 {{ $review->user?->activeFrame ? 'scale-[1.0475]' : 'ring-2 ring-white hover:ring-sky-200' }}">
                    @if($review->user?->avatar)
                        <img src="{{ $review->user->avatar }}" class="w-full h-full object-cover" alt="" loading="lazy">
                    @else
                        <span class="text-sm font-bold text-gray-500">{{ strtoupper(substr($review->user?->name ?? '?', 0, 1)) }}</span>
                    @endif
                </div>
                @if($review->user?->activeFrame)
                    <img src="{{ Storage::url($review->user->activeFrame->image_path) }}" alt="" 
                         class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[126%] h-[126%] max-w-none object-contain pointer-events-none z-10 transition-all duration-300">
                @endif
            </div>
            <div class="min-w-0">
                <a href="{{ route('profile.show', $review->user) }}" class="block text-sm font-semibold text-gray-900 truncate hover:text-sky-500 transition-colors">{{ $review->user?->name ?? 'Ẩn danh' }}</a>
                <p class="text-xs text-gray-400">{{ $review->published_at?->diffForHumans() }}</p>
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
            class="flex items-center gap-2 text-xs font-medium text-sky-500 hover:text-sky-600 transition-colors mb-2 bg-sky-50 w-fit px-2 py-1 rounded-md">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/></svg>
            {{ $review->movie->title }}
        </a>
    @endif

    {{-- Review title --}}
    @if($review->title)
        <h4 class="font-heading font-bold text-gray-900 text-sm mb-1 group-hover:text-sky-500 transition-colors">{{ $review->title }}</h4>
    @endif

    {{-- Review content --}}
    @if($review->is_spoiler)
        <x-spoiler-toggle>
            <p class="text-gray-600 text-sm leading-relaxed line-clamp-3">
                {{ $review->excerpt ?? Str::limit($review->content, 150) }}
            </p>
        </x-spoiler-toggle>
    @else
        <p class="text-gray-600 text-sm leading-relaxed line-clamp-3">
            {{ $review->excerpt ?? Str::limit($review->content, 150) }}
        </p>
    @endif

    {{-- Rating label --}}
    @if($review->rating)
        <p class="text-xs mt-2 mb-4 font-medium {{ str_replace('rating-bg-', 'rating-', $ratingColor) }}">{{ $ratingLabel }}</p>
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
    }" class="mt-4 pt-4 border-t border-gray-100">
        <div class="flex items-center gap-4">
            {{-- Like Button --}}
            <button @click="toggleLike()" class="flex items-center gap-1.5 text-sm font-medium transition-colors group"
                :class="liked ? 'text-sky-500' : 'text-gray-500 hover:text-gray-900'">
                <svg class="w-4 h-4 transition-transform group-active:scale-75" :fill="liked ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                <span x-text="likesCount > 0 ? likesCount : 'Thích'"></span>
            </button>

            {{-- Comment Toggle --}}
            <button @click="showComments = !showComments" class="flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <span>{{ $review->comments->count() > 0 ? $review->comments->count() : 'Bình luận' }}</span>
            </button>
        </div>

        {{-- Expanded Comments Section --}}
        <div x-show="showComments" x-collapse class="mt-4 pt-4 border-t border-gray-100" style="display: none;">
            {{-- List existing comments --}}
            @if($review->comments->isNotEmpty())
                <div class="space-y-4 mb-4">
                    @foreach($review->comments as $comment)
                        <div class="flex gap-3">
                            <div class="relative group w-8 h-8 shrink-0">
                                <div class="w-full h-full rounded-full bg-gray-100 flex items-center justify-center overflow-hidden text-center leading-8 text-[10px] font-bold text-gray-500 transition-all duration-300 {{ $comment->user->activeFrame ? 'scale-[1.0475]' : 'ring-1 ring-gray-200 hover:ring-sky-300' }}">
                                    @if($comment->user->avatar)
                                        <img src="{{ $comment->user->avatar }}" class="w-full h-full object-cover" loading="lazy">
                                    @else
                                        {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                    @endif
                                </div>
                                @if($comment->user->activeFrame)
                                    <img src="{{ Storage::url($comment->user->activeFrame->image_path) }}" alt="" 
                                         class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[126%] h-[126%] max-w-none object-contain pointer-events-none z-10 transition-all duration-300">
                                @endif
                            </div>
                            <div class="flex-1 bg-gray-50 border border-gray-100 rounded-xl p-3">
                                <div class="flex items-center justify-between mb-1">
                                    <a href="{{ route('profile.show', $comment->user) }}" class="text-xs font-bold text-gray-900 hover:text-sky-600 hover:underline transition-colors">{{ $comment->user->name }}</a>
                                    <span class="text-[10px] text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-sm text-gray-700">{{ $comment->content }}</p>
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
                    <div class="relative group w-8 h-8 shrink-0">
                        <div class="w-full h-full rounded-full bg-gray-100 flex items-center justify-center overflow-hidden text-center leading-8 text-[10px] font-bold text-gray-500 transition-all duration-300 {{ Auth::user()->activeFrame ? 'scale-[1.0475]' : 'ring-1 ring-gray-200' }}">
                            @if(Auth::user()->avatar)
                                <img src="{{ Auth::user()->avatar }}" class="w-full h-full object-cover" loading="lazy">
                            @else
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            @endif
                        </div>
                        @if(Auth::user()->activeFrame)
                            <img src="{{ Storage::url(Auth::user()->activeFrame->image_path) }}" alt="" 
                                 class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[126%] h-[126%] max-w-none object-contain pointer-events-none z-10 transition-all duration-300">
                        @endif
                    </div>
                    <div class="flex-1 flex gap-2">
                        <input type="text" name="content" required placeholder="Viết bình luận..." class="w-full bg-gray-50 border border-gray-200 rounded-xl py-1.5 px-3 text-sm flex-1 focus:border-sky-300 focus:ring-1 focus:ring-sky-300 outline-none transition-colors">
                        <button type="submit" class="shrink-0 px-3 py-1.5 rounded-xl bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium transition-colors">Gửi</button>
                    </div>
                </form>
            @else
                <p class="text-xs text-center text-gray-500">Vui lòng <a href="{{ route('login') }}" class="text-sky-500 hover:underline font-medium">đăng nhập</a> để bình luận.</p>
            @endauth
        </div>
    </div>
</div>
