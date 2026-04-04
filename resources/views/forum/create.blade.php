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
                <div>
                    <label for="forum_category_id" class="block text-sm font-medium text-gray-700 mb-2">Chuyên mục</label>
                    <select id="forum_category_id" name="forum_category_id" required
                            class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm text-gray-800
                                   focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-400 transition-all shadow-sm">
                        <option value="">— Chọn chuyên mục —</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('forum_category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
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

                {{-- Content --}}
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Nội dung</label>
                    <textarea id="content" name="content" rows="10"
                              class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm text-gray-800
                                     placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-400 transition-all resize-none shadow-sm"
                              placeholder="Viết nội dung bài viết của bạn..." required minlength="10">{{ old('content') }}</textarea>
                    @error('content')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                    <a href="{{ route('forum.index') }}" class="text-sm text-gray-500 hover:text-gray-900 transition-colors font-medium">
                        ← Quay lại
                    </a>
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-1.5 px-8 py-2.5 bg-sky-500 text-white text-sm font-semibold rounded-xl
                                   hover:bg-sky-600 transition-all duration-200 shadow-sm hover:shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Đăng bài
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

</x-app-layout>
