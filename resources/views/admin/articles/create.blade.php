<x-admin-layout :title="'Viết bài mới'" pageTitle="Viết bài mới">

<form action="{{ route('admin.articles.store') }}" method="POST" class="max-w-4xl">
    @csrf

    <div class="space-y-6">
        {{-- Title --}}
        <div>
            <label for="title" class="block text-sm font-medium text-dark-300 mb-1.5">Tiêu đề <span class="text-red-400">*</span></label>
            <input type="text" name="title" id="title" value="{{ old('title') }}" required maxlength="255"
                   class="w-full px-4 py-2.5 bg-dark-800 border border-dark-700 rounded-xl text-white text-sm
                          focus:outline-none focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500 transition-all"
                   placeholder="Nhập tiêu đề bài viết">
            @error('title') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
        </div>

        {{-- Subtitle --}}
        <div>
            <label for="subtitle" class="block text-sm font-medium text-dark-300 mb-1.5">Phụ đề</label>
            <input type="text" name="subtitle" id="subtitle" value="{{ old('subtitle') }}" maxlength="500"
                   class="w-full px-4 py-2.5 bg-dark-800 border border-dark-700 rounded-xl text-white text-sm
                          focus:outline-none focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500 transition-all"
                   placeholder="Mô tả ngắn về bài viết (tùy chọn)">
            @error('subtitle') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
        </div>

        {{-- Content --}}
        <div>
            <label for="content" class="block text-sm font-medium text-dark-300 mb-1.5">Nội dung <span class="text-red-400">*</span></label>
            <textarea name="content" id="content" rows="15" required
                      class="w-full px-4 py-3 bg-dark-800 border border-dark-700 rounded-xl text-white text-sm leading-relaxed
                             focus:outline-none focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500 transition-all resize-y"
                      placeholder="Nội dung chính của bài viết...">{{ old('content') }}</textarea>
            @error('content') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
        </div>

        {{-- Thumbnail --}}
        <div>
            <label for="thumbnail" class="block text-sm font-medium text-dark-300 mb-1.5">Ảnh bìa (URL)</label>
            <input type="url" name="thumbnail" id="thumbnail" value="{{ old('thumbnail') }}" maxlength="500"
                   class="w-full px-4 py-2.5 bg-dark-800 border border-dark-700 rounded-xl text-white text-sm
                          focus:outline-none focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500 transition-all"
                   placeholder="https://example.com/image.jpg">
            @error('thumbnail') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
        </div>

        {{-- Tags --}}
        <div>
            <label for="tags" class="block text-sm font-medium text-dark-300 mb-1.5">Từ khóa</label>
            <input type="text" name="tags" id="tags" value="{{ old('tags') }}"
                   class="w-full px-4 py-2.5 bg-dark-800 border border-dark-700 rounded-xl text-white text-sm
                          focus:outline-none focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500 transition-all"
                   placeholder="Phân tách bằng dấu phẩy, ví dụ: HÀNH ĐỘNG, PHIM MỚI, TIN TỨC">
            <p class="mt-1 text-xs text-dark-500">Nhập các từ khóa, phân tách bằng dấu phẩy. Ví dụ: HÀNH ĐỘNG, PHIM MỚI</p>
            @error('tags') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror

            {{-- Existing tags suggestions --}}
            @if($tags->isNotEmpty())
                <div class="mt-2 flex flex-wrap gap-1.5">
                    @foreach($tags as $tag)
                        <button type="button"
                                onclick="addTag('{{ $tag->name }}')"
                                class="px-2 py-0.5 bg-dark-700 text-dark-300 text-[11px] font-semibold rounded-md uppercase hover:bg-dark-600 hover:text-white transition-colors cursor-pointer">
                            + {{ $tag->name }}
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Published --}}
        <div class="flex items-center gap-3">
            <input type="hidden" name="is_published" value="0">
            <input type="checkbox" name="is_published" id="is_published" value="1"
                   {{ old('is_published') ? 'checked' : '' }}
                   class="w-4 h-4 rounded bg-dark-800 border-dark-600 text-rose-500 focus:ring-rose-500/30 focus:ring-offset-0">
            <label for="is_published" class="text-sm text-dark-300">Đăng ngay (công khai)</label>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-3 pt-4 border-t border-dark-800">
            <button type="submit"
                    class="px-6 py-2.5 bg-rose-600 text-white text-sm font-medium rounded-xl hover:bg-rose-700 active:scale-[0.97] transition-all">
                Tạo bài viết
            </button>
            <a href="{{ route('admin.articles.index') }}"
               class="px-4 py-2.5 text-dark-400 text-sm hover:text-white transition-colors">
                Hủy
            </a>
        </div>
    </div>
</form>

<script>
function addTag(tagName) {
    const input = document.getElementById('tags');
    const currentTags = input.value.split(',').map(t => t.trim()).filter(t => t);
    if (!currentTags.includes(tagName)) {
        currentTags.push(tagName);
        input.value = currentTags.join(', ');
    }
}
</script>

</x-admin-layout>
