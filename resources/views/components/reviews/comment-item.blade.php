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
                    $formattedContent = preg_replace($pattern, '<span class="text-sky-600 bg-sky-100/80 px-1.5 py-0.5 rounded-md font-semibold cursor-pointer hover:bg-sky-200/60 transition-colors">$1</span>', $formattedContent);
                }
            }
        @endphp
        <p class="text-sm text-gray-700 whitespace-pre-line break-words">{!! $formattedContent !!}</p>
        
        <div class="mt-1.5 flex items-center gap-3">
            <button type="button" @click="focusReply('{{ addslashes($comment->user?->name ?? 'Ẩn danh') }}')" class="text-[11px] font-semibold text-gray-500 hover:text-gray-700 transition-colors whitespace-nowrap">Trả lời</button>
            @if(Auth::check() && Auth::id() === $comment->user_id)
                <button type="button" @click="openDeleteModal('{{ $comment->uuid }}')" class="text-[11px] font-semibold text-red-500 hover:text-red-700 transition-colors whitespace-nowrap">Xóa</button>
            @endif
        </div>
    </div>
</div>
