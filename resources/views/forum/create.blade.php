<x-app-layout>
    <x-slot:title>Tạo bài viết mới — Diễn đàn</x-slot:title>

<section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-dark-500 mb-8">
        <a href="{{ route('forum.index') }}" class="hover:text-white transition-colors">Diễn đàn</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-dark-400">Tạo bài viết mới</span>
    </nav>

    <div class="card p-6 sm:p-8">
        <h1 class="text-2xl font-display font-bold text-white mb-6">Tạo bài viết mới</h1>

        <form action="{{ route('forum.storeThread') }}" method="POST" class="space-y-5">
            @csrf

            {{-- Category --}}
            <div>
                <label for="forum_category_id" class="block text-sm font-medium text-dark-200 mb-2">Chuyên mục</label>
                <select id="forum_category_id" name="forum_category_id" class="input-dark text-sm" required>
                    <option value="">— Chọn chuyên mục —</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('forum_category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                @error('forum_category_id')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Title --}}
            <div>
                <label for="title" class="block text-sm font-medium text-dark-200 mb-2">Tiêu đề</label>
                <input id="title" type="text" name="title" value="{{ old('title') }}"
                       class="input-dark text-sm" placeholder="Tiêu đề bài viết..." required minlength="5" maxlength="255">
                @error('title')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Content --}}
            <div>
                <label for="content" class="block text-sm font-medium text-dark-200 mb-2">Nội dung</label>
                <textarea id="content" name="content" rows="10"
                          class="input-dark text-sm resize-none"
                          placeholder="Viết nội dung bài viết của bạn..." required minlength="10">{{ old('content') }}</textarea>
                @error('content')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-between pt-4 border-t border-dark-700">
                <a href="{{ route('forum.index') }}" class="text-sm text-dark-400 hover:text-white transition-colors">
                    ← Quay lại
                </a>
                <button type="submit" class="btn-rose text-sm py-2.5 px-8">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Đăng bài
                </button>
            </div>
        </form>
    </div>
</section>

</x-app-layout>
