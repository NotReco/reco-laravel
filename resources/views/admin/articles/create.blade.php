<x-admin-layout :title="'Viết bài mới'" pageTitle="Viết bài mới">

    <form action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data" class="max-w-4xl"
        id="article-form">
        @csrf

        <div class="space-y-6">
            {{-- Title --}}
            <div>
                <label for="title" class="block text-sm font-medium text-dark-300 mb-1.5">Tiêu đề <span
                        class="text-red-400">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required maxlength="255"
                    class="w-full px-4 py-2.5 bg-dark-800 border border-dark-700 rounded-xl text-white text-sm
                          focus:outline-none focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500 transition-all">
                @error('title')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Subtitle --}}
            <div>
                <label for="subtitle" class="block text-sm font-medium text-dark-300 mb-1.5">Phụ đề</label>
                <input type="text" name="subtitle" id="subtitle" value="{{ old('subtitle') }}" maxlength="500"
                    class="w-full px-4 py-2.5 bg-dark-800 border border-dark-700 rounded-xl text-white text-sm
                          focus:outline-none focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500 transition-all">
                @error('subtitle')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Content --}}
            <div>
                <label for="content" class="block text-sm font-medium text-dark-300 mb-1.5">Nội dung <span
                        class="text-red-400">*</span></label>
                <textarea name="content" id="content" rows="15"
                    class="js-richtext w-full px-4 py-3 bg-dark-800 border border-dark-700 rounded-xl text-white text-sm leading-relaxed
                             focus:outline-none focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500 transition-all resize-y"
                    data-richtext-height="520" placeholder="Nội dung chính của bài viết...">{{ old('content') }}</textarea>
                <p class="mt-2.5 text-xs text-dark-500 italic">Cỡ chữ chỉnh ở Styles. Chèn ảnh/video bằng cách dán trực
                    tiếp hoặc dùng nút tương ứng trên thanh công cụ (Video tối đa 50MB).</p>
                @error('content')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="rounded-xl border border-dark-700 bg-dark-800/40 p-5 space-y-4">
                <h3 class="text-sm font-semibold text-white">Điểm đánh giá đa nguồn</h3>
                <p class="text-xs text-dark-500 italic">Nhập tay theo nguồn tham chiếu lúc đăng bài. Để trống nếu không
                    dùng. Ví dụ: <span class="text-dark-400">9.7</span>, <span class="text-dark-400">80</span>, <span
                        class="text-dark-400">93%</span>.</p>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label for="rating_reco"
                            class="block text-xs font-medium text-dark-400 mb-1">{{ config('app.name', 'Reco') }}</label>
                        <input type="text" name="rating_reco" id="rating_reco" value="{{ old('rating_reco') }}"
                            maxlength="32"
                            class="w-full px-3 py-2 bg-dark-800 border border-dark-700 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500">
                        @error('rating_reco')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="rating_imdb" class="block text-xs font-medium text-dark-400 mb-1">IMDb</label>
                        <input type="text" name="rating_imdb" id="rating_imdb" value="{{ old('rating_imdb') }}"
                            maxlength="32"
                            class="w-full px-3 py-2 bg-dark-800 border border-dark-700 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500">
                        @error('rating_imdb')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="rating_metacritic"
                            class="block text-xs font-medium text-dark-400 mb-1">Metacritic</label>
                        <input type="text" name="rating_metacritic" id="rating_metacritic"
                            value="{{ old('rating_metacritic') }}" maxlength="32"
                            class="w-full px-3 py-2 bg-dark-800 border border-dark-700 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500">
                        @error('rating_metacritic')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="rating_rotten_tomatoes" class="block text-xs font-medium text-dark-400 mb-1">Rotten
                            Tomatoes</label>
                        <input type="text" name="rating_rotten_tomatoes" id="rating_rotten_tomatoes"
                            value="{{ old('rating_rotten_tomatoes') }}" maxlength="32"
                            class="w-full px-3 py-2 bg-dark-800 border border-dark-700 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500">
                        @error('rating_rotten_tomatoes')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="rating_tmdb" class="block text-xs font-medium text-dark-400 mb-1">TMDb</label>
                        <input type="text" name="rating_tmdb" id="rating_tmdb" value="{{ old('rating_tmdb') }}"
                            maxlength="32"
                            class="w-full px-3 py-2 bg-dark-800 border border-dark-700 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500">
                        @error('rating_tmdb')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Thumbnail: URL hoặc upload --}}
            <div class="space-y-3">
                <label class="block text-sm font-medium text-dark-300 mb-1.5">Ảnh bìa</label>
                <div>
                    <label for="thumbnail_upload" class="block text-xs font-medium text-dark-400 mb-1">Ảnh bìa từ
                        máy</label>
                    <input type="file" name="thumbnail_upload" id="thumbnail_upload"
                        accept="image/jpeg,image/png,image/webp,image/gif"
                        class="block w-full text-sm text-dark-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-dark-700 file:text-white hover:file:bg-dark-600 file:cursor-pointer cursor-pointer">
                    <p class="mt-2 text-[11px] text-dark-500 italic">JPEG, PNG, WebP hoặc GIF — tối đa 3&nbsp;MB. Có
                        file upload thì ưu tiên hơn URL bên dưới.</p>
                    @error('thumbnail_upload')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="thumbnail" class="block text-xs font-medium text-dark-400 mb-1">Ảnh bìa URL</label>
                    <input type="text" name="thumbnail" id="thumbnail" value="{{ old('thumbnail') }}"
                        maxlength="500" inputmode="url" autocomplete="off"
                        class="w-full px-4 py-2.5 bg-dark-800 border border-dark-700 rounded-xl text-white text-sm
                              focus:outline-none focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500 transition-all">
                    @error('thumbnail')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Tags --}}
            <div>
                <label for="tags" class="block text-sm font-medium text-dark-300 mb-1.5">Từ khóa</label>
                <input type="text" name="tags" id="tags" value="{{ old('tags') }}"
                    class="w-full px-4 py-2.5 bg-dark-800 border border-dark-700 rounded-xl text-white text-sm
                          focus:outline-none focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500 transition-all">
                <p class="mt-1 text-xs text-dark-500">Nhập các từ khóa, phân tách bằng dấu phẩy. Ví dụ: HÀNH ĐỘNG, PHIM
                    MỚI.</p>
                @error('tags')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror

                {{-- Existing tags suggestions --}}
                @if ($tags->isNotEmpty())
                    <div class="mt-2 flex flex-wrap gap-1.5">
                        @foreach ($tags as $tag)
                            <button type="button" data-tag-name="{{ $tag->name }}"
                                onclick="addTagFromButton(this)"
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
                <label for="is_published" class="text-sm text-dark-300">Công khai ngay</label>
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
                <span id="autosave-indicator"
                    class="ml-auto text-xs text-dark-400 opacity-0 transition-opacity duration-300"></span>
            </div>
        </div>
    </form>

    <script>
        function addTag(tagName) {
            const input = document.getElementById('tags');
            if (!input || !tagName) return;
            const currentTags = input.value.split(',').map(t => t.trim()).filter(t => t);
            if (!currentTags.includes(tagName)) {
                currentTags.push(tagName);
                input.value = currentTags.join(', ');
            }
        }

        function addTagFromButton(btn) {
            addTag(btn.getAttribute('data-tag-name') || '');
        }
    </script>

    @include('admin.articles.partials.autosave', ['autosaveKey' => 'autosave_article_create'])

</x-admin-layout>
