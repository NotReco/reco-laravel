@props(['comment', 'review'])

<div id="review-comment-{{ $comment->uuid }}" class="flex gap-3">
    <div class="relative group w-8 h-8 shrink-0">
        <div class="w-full h-full rounded-full bg-gray-100 flex items-center justify-center overflow-hidden text-center leading-8 text-[10px] font-bold text-gray-500 transition-all duration-300 {{ $comment->user?->activeFrame ? 'scale-[1.0475]' : 'ring-1 ring-gray-200 hover:ring-sky-300' }}">
            @if($comment->user?->avatar)
                <img src="{{ $comment->user->avatar }}" class="w-full h-full object-cover" loading="lazy">
            @else
                {{ strtoupper(substr($comment->user?->name ?? '?', 0, 1)) }}
            @endif
        </div>
        @if($comment->user?->activeFrame)
            <img src="{{ Storage::url($comment->user->activeFrame->image_path) }}" alt="" 
                 class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[126%] h-[126%] max-w-none object-contain pointer-events-none z-10 transition-all duration-300">
        @endif
    </div>
    <div class="flex-1 bg-gray-50 border border-gray-100 rounded-xl p-3">
        <div class="flex items-center justify-between mb-1">
            <a href="{{ route('profile.show', $comment->user ?? '#') }}" class="text-xs font-bold text-gray-900 hover:text-sky-600 hover:underline transition-colors">{{ $comment->user?->name ?? 'Ẩn danh' }}</a>
            <span class="text-[10px] text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
        </div>
        @php
            $formattedContent = e($comment->content);
            if(isset($review) && $review->comments) {
                $pNames = $review->comments->pluck('user.name')->filter()->unique()->sortByDesc(function($n) { return mb_strlen($n); });
                foreach($pNames as $nm) {
                    $pattern = '/(@' . preg_quote($nm, '/') . ')(?![\\p{L}\\p{N}_])/u';
                    $formattedContent = preg_replace($pattern, '<span class="text-[#0866FF] font-semibold hover:underline cursor-pointer">$1</span>', $formattedContent);
                }
            }
        @endphp
        <p class="text-sm text-gray-700 whitespace-pre-line break-words">{!! $formattedContent !!}</p>
        
        <div class="mt-1.5 flex items-center justify-between" x-data="{ 
            liked: {{ auth()->check() && $comment->likes->contains('user_id', auth()->id()) ? 'true' : 'false' }}, 
            likesCount: {{ $comment->likes_count ?? 0 }}, 
            isLiking: false 
        }">
            <div class="flex items-center gap-3">
                <button type="button" 
                    @click="if(isLiking) return; isLiking = true; fetch('{{ route('comments.like', $comment) }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } }).then(r => { if(r.status === 401) { window.location.href = '{{ route('login') }}'; return Promise.reject(); } return r.json(); }).then(d => { liked = d.isLiked; likesCount = d.likesCount; }).catch(e => console.error(e)).finally(() => isLiking = false);"
                    class="text-[11px] font-semibold transition-colors whitespace-nowrap"
                    :class="liked ? 'text-rose-500' : 'text-gray-500 hover:text-gray-700'">
                    Thích
                </button>
                <button type="button" @click="focusReply('{{ addslashes($comment->user?->name ?? 'Ẩn danh') }}')" class="text-[11px] font-semibold text-gray-500 hover:text-gray-700 transition-colors whitespace-nowrap">Trả lời</button>
                @if(auth()->check() && auth()->id() !== $comment->user_id)
                    <button type="button" @click="$dispatch('open-report', { type: 'Comment', id: {{ $comment->id }} })" class="text-[11px] font-semibold text-gray-500 hover:text-gray-700 transition-colors whitespace-nowrap">Báo cáo</button>
                @endif
                @if(Auth::check() && Auth::id() === $comment->user_id)
                    <button type="button" @click="openDeleteModal('{{ $comment->uuid }}')" class="text-[11px] font-semibold text-red-500 hover:text-red-700 transition-colors whitespace-nowrap">Xóa</button>
                @endif
            </div>

            <div x-show="likesCount > 0" x-cloak style="{{ ($comment->likes_count ?? 0) > 0 ? '' : 'display: none;' }}" class="flex items-center gap-1 cursor-pointer">
                <span x-text="likesCount" class="text-[11px] text-gray-500 hover:underline">{{ $comment->likes_count ?: '' }}</span>
                <div class="w-[14px] h-[14px] rounded-full bg-rose-500 flex items-center justify-center shadow-sm">
                    <svg class="w-2 h-2 text-white fill-current" viewBox="0 0 24 24">
                        <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>
