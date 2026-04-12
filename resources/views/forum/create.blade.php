<x-app-layout>
    <x-slot:title>Tạo bài viết mới — Diễn đàn</x-slot:title>

<div class="bg-gray-50 min-h-screen pb-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-gray-500 mb-8">
            <a href="{{ route('forum.index') }}" class="hover:text-gray-900 transition-colors">Diễn đàn</a>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-700 font-medium">Tạo bài viết mới</span>
        </nav>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8">
            <h1 class="text-2xl font-display font-bold text-gray-900 mb-6">Tạo bài viết mới</h1>

            <form action="{{ route('forum.storeThread') }}" method="POST" class="space-y-5">
                @csrf

                {{-- Category --}}
                <div x-data="{ open: false, selected: '{{ old('forum_category_id', '') }}', selectedName: '' }" x-init="
                    if (selected) {
                        const opt = $refs.catList?.querySelector('[data-value=\"' + selected + '\"');
                        if (opt) selectedName = opt.textContent.trim();
                    }
                " class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Chuyên mục</label>
                    <input type="hidden" name="forum_category_id" :value="selected" required>
                    <button type="button" @click="open = !open" @click.outside="open = false"
                            class="w-full flex items-center justify-between px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm
                                   focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-400 transition-all shadow-sm cursor-pointer">
                        <span :class="selected ? 'text-gray-800' : 'text-gray-400'" x-text="selected ? selectedName : '— Chọn chuyên mục —'"></span>
                        <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                         class="absolute z-20 mt-1.5 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden" x-cloak>
                        <div x-ref="catList" class="py-1 max-h-60 overflow-y-auto">
                            @foreach($categories as $cat)
                                <button type="button" data-value="{{ $cat->id }}"
                                        @click="selected = '{{ $cat->id }}'; selectedName = '{{ $cat->name }}'; open = false"
                                        class="w-full text-left px-4 py-2.5 text-sm hover:bg-sky-50 hover:text-sky-600 transition-colors"
                                        :class="selected === '{{ $cat->id }}' ? 'bg-sky-50 text-sky-600 font-medium' : 'text-gray-700'">
                                    {{ $cat->name }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                    @error('forum_category_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Title --}}
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Tiêu đề</label>
                    <input id="title" type="text" name="title" value="{{ old('title') }}"
                           class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm text-gray-800
                                  placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-400 transition-all shadow-sm"
                           placeholder="Tiêu đề bài viết..." required minlength="5" maxlength="255">
                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Nội dung</label>
                    <textarea id="content" name="content" rows="10"
                              class="js-richtext-simple w-full"
                              data-richtext-height="350"
                              placeholder="Viết nội dung bài viết của bạn..." required minlength="10">{!! old('content') !!}</textarea>
                    @error('content')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-between pt-2">
                    <a href="{{ route('forum.index') }}" class="text-sm text-gray-500 hover:text-gray-900 transition-colors font-medium">
                        ← Quay lại
                    </a>
                    <button type="submit"
                            class="px-7 py-2.5 bg-sky-500 text-white text-sm font-semibold rounded-xl
                                   hover:bg-sky-600 transition-all duration-200 shadow-sm hover:shadow-md">
                        Đăng bài
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('partials.tinymce-simple')
</x-app-layout>
