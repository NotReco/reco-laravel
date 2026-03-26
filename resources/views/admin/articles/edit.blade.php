<x-admin-layout :title="'Chỉnh sửa bài viết'" pageTitle="Chỉnh sửa bài viết">

<form action="{{ route('admin.articles.update', $article) }}" method="POST" class="max-w-4xl">
    @csrf
    @method('PUT')

    <div class="space-y-6">
        {{-- Title --}}
        <div>
            <label for="title" class="block text-sm font-medium text-dark-300 mb-1.5">Tiêu đề <span class="text-red-400">*</span></label>
            <input type="text" name="title" id="title" value="{{ old('title', $article->title) }}" required maxlength="255"
                   class="w-full px-4 py-2.5 bg-dark-800 border border-dark-700 rounded-xl text-white text-sm
                          focus:outline-none focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500 transition-all">
            @error('title') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
        </div>

        {{-- Subtitle --}}
        <div>
            <label for="subtitle" class="block text-sm font-medium text-dark-300 mb-1.5">Phụ đề</label>
            <input type="text" name="subtitle" id="subtitle" value="{{ old('subtitle', $article->subtitle) }}" maxlength="500"
                   class="w-full px-4 py-2.5 bg-dark-800 border border-dark-700 rounded-xl text-white text-sm
                          focus:outline-none focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500 transition-all">
            @error('subtitle') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
        </div>

        {{-- Content --}}
        <div>
            <label for="content" class="block text-sm font-medium text-dark-300 mb-1.5">Nội dung <span class="text-red-400">*</span></label>
            <textarea name="content" id="content" rows="15" required
                      class="w-full px-4 py-3 bg-dark-800 border border-dark-700 rounded-xl text-white text-sm leading-relaxed
                             focus:outline-none focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500 transition-all resize-y">{{ old('content', $article->content) }}</textarea>
            @error('content') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
        </div>

        {{-- Thumbnail --}}
        <div>
            <label for="thumbnail" class="block text-sm font-medium text-dark-300 mb-1.5">Ảnh bìa (URL)</label>
            @if($article->thumbnail)
                <div class="mb-2 rounded-lg overflow-hidden inline-block border border-dark-700">
                    <img src="{{ $article->thumbnail }}" alt="" class="h-28 object-cover">
                </div>
            @endif
            <input type="url" name="thumbnail" id="thumbnail" value="{{ old('thumbnail', $article->thumbnail) }}" maxlength="500"
                   class="w-full px-4 py-2.5 bg-dark-800 border border-dark-700 rounded-xl text-white text-sm
                          focus:outline-none focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500 transition-all"
                   placeholder="https://example.com/image.jpg">
            @error('thumbnail') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
        </div>

        {{-- Tags --}}
        <div>
            <label for="tags" class="block text-sm font-medium text-dark-300 mb-1.5">Từ khóa</label>
            <input type="text" name="tags" id="tags"
                   value="{{ old('tags', $article->tags->pluck('name')->implode(', ')) }}"
                   class="w-full px-4 py-2.5 bg-dark-800 border border-dark-700 rounded-xl text-white text-sm
                          focus:outline-none focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500 transition-all"
                   placeholder="Phân tách bằng dấu phẩy, ví dụ: HÀNH ĐỘNG, PHIM MỚI">
            <p class="mt-1 text-xs text-dark-500">Nhập các từ khóa, phân tách bằng dấu phẩy</p>
            @error('tags') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror

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
                   {{ old('is_published', $article->is_published) ? 'checked' : '' }}
                   class="w-4 h-4 rounded bg-dark-800 border-dark-600 text-rose-500 focus:ring-rose-500/30 focus:ring-offset-0">
            <label for="is_published" class="text-sm text-dark-300">Đã đăng (công khai)</label>
        </div>

        {{-- Info --}}
        <div class="text-xs text-dark-500 space-y-1 bg-dark-800/50 rounded-xl px-4 py-3 border border-dark-800">
            <p>Slug: <span class="text-dark-300 font-mono">{{ $article->slug }}</span></p>
            <p>Tạo bởi: <span class="text-dark-300">{{ $article->user->name ?? 'Ẩn danh' }}</span></p>
            <p>Ngày tạo: <span class="text-dark-300">{{ $article->created_at->format('d/m/Y H:i') }}</span></p>
            @if($article->published_at)
                <p>Ngày đăng: <span class="text-dark-300">{{ $article->published_at->format('d/m/Y H:i') }}</span></p>
            @endif
            <p>Lượt xem: <span class="text-dark-300">{{ number_format($article->views_count) }}</span></p>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-3 pt-4 border-t border-dark-800">
            <button type="submit"
                    class="px-6 py-2.5 bg-rose-600 text-white text-sm font-medium rounded-xl hover:bg-rose-700 active:scale-[0.97] transition-all">
                Lưu thay đổi
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
