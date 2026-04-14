{{-- Review Form Component --}}
{{-- Usage: <x-review-form :movie="$movie" /> --}}

@props(['movie'])

@auth
<div class="card p-6" x-data="{
    score: 0,
    hover: 0,
    content: '',
    isSpoiler: false,
    maxLength: 500,
    get ratingColor() {
        const s = this.hover || this.score;
        if (s >= 9) return '#f5c518';
        if (s >= 7) return '#2A9D8F';
        if (s >= 5) return '#d97b2a';
        if (s >= 1) return '#E63946';
        return '#6c757d';
    },
    get ratingLabel() {
        const s = this.hover || this.score;
        const labels = ['', 'Rất tệ', 'Tệ', 'Dưới TB', 'Tạm được', 'Trung bình', 'Khá', 'Tốt', 'Rất tốt', 'Xuất sắc', 'Kiệt tác'];
        return labels[s] || '';
    }
}">
    <h3 class="text-lg font-display font-semibold text-white mb-4">✍️ Viết đánh giá</h3>

    <form action="{{ route('reviews.store', $movie) }}" method="POST">
        @csrf

        {{-- Star Rating --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-dark-200 mb-2">Đánh giá của bạn</label>
            <div class="flex items-center gap-3">
                <div class="flex gap-1">
                    <template x-for="i in 10" :key="i">
                        <button type="button" @click="score = i" @mouseenter="hover = i" @mouseleave="hover = 0"
                            class="w-8 h-8 rounded-lg flex items-center justify-center transition-all duration-150 focus:outline-none"
                            :class="i <= (hover || score)
                                ? 'scale-110'
                                : 'bg-dark-700 hover:bg-dark-600'"
                            :style="i <= (hover || score) ? 'background-color: ' + ratingColor : ''"
                        >
                            <svg class="w-4 h-4" :class="i <= (hover || score) ? 'text-white' : 'text-dark-400'" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </button>
                    </template>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xl font-bold text-white" x-text="(hover || score) ? (hover || score) + '/10' : ''"></span>
                    <span class="text-sm font-medium" :style="'color: ' + ratingColor" x-text="ratingLabel"></span>
                </div>
            </div>
            <input type="hidden" name="rating" :value="score">
            <x-input-error :messages="$errors->get('rating')" class="mt-1" />
        </div>

        {{-- Title (optional) --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-dark-200 mb-2">Tiêu đề <span class="text-dark-500">(tuỳ chọn)</span></label>
            <input type="text" name="title" class="input-dark" placeholder="Tóm tắt cảm nhận..." maxlength="255">
        </div>

        {{-- Content --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-dark-200 mb-2">Nội dung review <span class="text-dark-500">(tuỳ chọn)</span></label>
            <textarea name="content" rows="4" class="input-dark resize-none" x-model="content" @keydown.enter="if(!$event.shiftKey) { $event.preventDefault(); $el.closest('form').submit(); }"
                :maxlength="maxLength"
                placeholder="Chia sẻ cảm nhận của bạn về bộ phim..."></textarea>
            <div class="flex items-center justify-between mt-1">
                <x-input-error :messages="$errors->get('content')" />
                <span class="text-xs text-dark-500 ml-auto" x-text="content.length + '/' + maxLength"></span>
            </div>
        </div>

        {{-- Spoiler checkbox --}}
        <div class="mb-5">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_spoiler" x-model="isSpoiler"
                    class="w-4 h-4 rounded border-dark-600 bg-dark-800 text-sky-600 focus:ring-sky-500 focus:ring-offset-dark-900">
                <span class="text-sm text-dark-300">⚠️ Chứa nội dung spoiler</span>
            </label>
        </div>

        {{-- Submit --}}
        <div class="flex items-center gap-3">
            <button type="submit" class="btn-sky" :disabled="!score">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                <span x-text="content.length > 0 ? 'Gửi review' : 'Chấm điểm'"></span>
            </button>
            <p class="text-xs text-dark-500" x-show="!content">Bạn có thể chỉ chấm điểm mà không viết review.</p>
        </div>
    </form>
</div>
@else
    <div class="card p-6 text-center">
        <p class="text-dark-400 mb-3">Đăng nhập để viết đánh giá</p>
        <a href="{{ route('login') }}" class="btn-sky text-sm">Đăng nhập</a>
    </div>
@endauth
