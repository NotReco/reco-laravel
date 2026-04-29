{{-- Review Form Component --}}
{{-- Usage: <x-review-form :movie="$movie" /> --}}

@props(['movie'])

@auth
<div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm" x-data="{
    score: 0,
    hover: 0,
    content: '',
    title: '',
    isSpoiler: false,
    maxLength: 500,
    get ratingColor() {
        const s = this.hover || this.score;
        if (s >= 9) return '#f59e0b';
        if (s >= 7) return '#10b981';
        if (s >= 5) return '#f97316';
        if (s >= 1) return '#ef4444';
        return '#9ca3af';
    },
    get ratingLabel() {
        const s = this.hover || this.score;
        const labels = ['', 'Rất tệ', 'Tệ', 'Dưới TB', 'Tạm được', 'Trung bình', 'Khá', 'Tốt', 'Rất tốt', 'Xuất sắc', 'Kiệt tác'];
        return labels[s] || '';
    }
}">

    <form action="{{ route('reviews.store', $movie) }}" method="POST">
        @csrf

        {{-- Star Rating --}}
        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-2.5">
                Đánh giá của bạn
                <span class="text-red-500 ml-0.5">*</span>
            </label>
            <div class="flex items-center gap-3 flex-wrap">
                <div class="flex gap-1">
                    <template x-for="i in 10" :key="i">
                        <button type="button" @click="score = i" @mouseenter="hover = i" @mouseleave="hover = 0"
                            class="w-8 h-8 rounded-lg flex items-center justify-center transition-all duration-150 focus:outline-none border"
                            :class="i <= (hover || score)
                                ? 'border-transparent scale-110 shadow-sm'
                                : 'bg-gray-50 border-gray-200 hover:border-gray-300 hover:bg-gray-100'"
                            :style="i <= (hover || score) ? 'background-color: ' + ratingColor + '22; border-color: ' + ratingColor : ''"
                        >
                            <svg class="w-4 h-4 transition-colors"
                                :class="i <= (hover || score) ? 'fill-current' : 'text-gray-400 fill-current'"
                                :style="i <= (hover || score) ? 'color: ' + ratingColor : ''"
                                viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </button>
                    </template>
                </div>
                <div class="flex items-center gap-1.5" x-show="hover || score" x-cloak>
                    <span class="text-lg font-bold text-gray-900" x-text="(hover || score) + '/10'"></span>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full"
                        :style="'color: ' + ratingColor + '; background-color: ' + ratingColor + '18'"
                        x-text="ratingLabel"></span>
                </div>
            </div>
            <input type="hidden" name="rating" :value="score">
            <x-input-error :messages="$errors->get('rating')" class="mt-1" />
        </div>

        {{-- Title --}}
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tiêu đề</label>
            <input type="text" name="title" x-model="title"
                class="w-full bg-gray-50 border border-gray-200 rounded-xl py-2.5 px-3.5 text-sm text-gray-900 placeholder-gray-400 focus:border-sky-400 focus:ring-2 focus:ring-sky-100 outline-none transition-all"
                placeholder="Tóm tắt cảm nhận của bạn..." maxlength="255">
        </div>

        {{-- Content --}}
        <div class="mb-2">
            <div class="flex items-center justify-between mb-1.5">
                <label class="text-sm font-semibold text-gray-700">Nội dung</label>
                <span class="text-xs font-semibold text-gray-500" x-text="'(' + content.length + '/' + maxLength + ')'"></span>
            </div>
            <textarea name="content" rows="4" x-model="content"
                class="w-full bg-gray-50 border border-gray-200 rounded-xl py-2.5 px-3.5 text-sm text-gray-900 placeholder-gray-400 focus:border-sky-400 focus:ring-2 focus:ring-sky-100 outline-none transition-all resize-none"
                :maxlength="maxLength"
                placeholder="Chia sẻ cảm nhận của bạn về bộ phim..."></textarea>
            <x-input-error :messages="$errors->get('content')" class="mt-1" />
        </div>

        {{-- Spoiler checkbox --}}
        <div class="mb-4">
            <label class="flex items-center gap-2.5 cursor-pointer group w-fit">
                <input type="checkbox" name="is_spoiler" x-model="isSpoiler"
                    class="w-4 h-4 rounded border-gray-300 text-sky-500 focus:ring-sky-400 focus:ring-offset-0 cursor-pointer">
                <span class="text-sm text-gray-600 group-hover:text-gray-800 transition-colors select-none">
                    Chứa nội dung tiết lộ
                </span>
            </label>
        </div>

        {{-- Submit --}}
        <div class="flex items-center gap-3">
            <button type="submit"
                class="px-5 py-2.5 bg-sky-500 hover:bg-sky-600 disabled:opacity-40 disabled:cursor-not-allowed text-white text-sm font-semibold rounded-xl shadow-sm shadow-sky-500/20 transition-all"
                :disabled="!score">
                Đánh giá
            </button>
        </div>
    </form>
</div>

@else
{{-- Chưa đăng nhập --}}
<div class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm text-center">
    <div class="w-14 h-14 bg-sky-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
        <svg class="w-7 h-7 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
        </svg>
    </div>
    <h4 class="text-base font-bold text-gray-900 mb-1.5">Chia sẻ cảm nhận của bạn</h4>
    <p class="text-sm text-gray-500 mb-5 leading-relaxed">
        Đăng nhập để viết đánh giá và<br>chấm điểm cho bộ phim này.
    </p>
    <div class="flex items-center justify-center gap-3">
        <a href="{{ route('login') }}"
            class="px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white text-sm font-semibold rounded-xl shadow-sm shadow-sky-500/20 transition-all">
            Đăng nhập
        </a>
        <a href="{{ route('register') }}"
            class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition-all">
            Đăng ký
        </a>
    </div>
</div>
@endauth
