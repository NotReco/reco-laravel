<x-admin-layout :title="'Sửa phim — ' . $movie->title" pageTitle="Sửa phim">

    <div class="max-w-4xl">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-dark-500 mb-6">
            <a href="{{ route('admin.movies.index') }}" class="hover:text-white transition-colors">Quản lý phim</a>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-dark-400 truncate">{{ $movie->title }}</span>
        </nav>

        {{-- Header card --}}
        <div class="card p-5 mb-6 flex items-center gap-4">
            @if ($movie->poster)
                <div class="w-16 h-24 rounded-lg bg-dark-700 bg-cover bg-center shrink-0"
                    style="background-image: url('{{ $movie->poster }}')"></div>
            @else
                <div class="w-16 h-24 rounded-lg bg-dark-700 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-dark-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                    </svg>
                </div>
            @endif
            <div>
                <h2 class="text-xl font-bold text-white">{{ $movie->title }}</h2>
                @if ($movie->original_title && $movie->original_title !== $movie->title)
                    <p class="text-sm text-dark-400">{{ $movie->original_title }}</p>
                @endif
                <div class="flex items-center gap-3 mt-2">
                    <span class="text-xs text-dark-500">TMDb ID: <span
                            class="text-dark-300">{{ $movie->tmdb_id ?? '—' }}</span></span>
                    <span class="text-xs text-dark-500">Tạo lúc: <span
                            class="text-dark-300">{{ $movie->created_at->format('d/m/Y') }}</span></span>
                </div>
            </div>
            <div class="ml-auto flex flex-col items-end gap-2">
                <a href="{{ route('movies.show', $movie) }}" target="_blank"
                    class="inline-flex items-center gap-1.5 text-sm text-dark-400 hover:text-sky-400 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    Xem trang
                </a>
                <a href="{{ route('admin.movies.index') }}"
                    class="inline-flex items-center gap-1.5 text-sm text-dark-400 hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Quay lại
                </a>
            </div>
        </div>

        <form action="{{ route('admin.movies.update', $movie) }}" method="POST">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- ── Cột chính ─────────────────────────────────────────── --}}
                <div class="lg:col-span-2 space-y-5">

                    {{-- Tên phim --}}
                    <div class="card p-5">
                        <h3 class="text-sm font-semibold text-dark-300 uppercase tracking-wide mb-4">Thông tin cơ bản
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <label for="title" class="block text-sm font-medium text-dark-200 mb-1.5">Tên phim
                                    <span class="text-red-400">*</span></label>
                                <input id="title" type="text" name="title"
                                    value="{{ old('title', $movie->title) }}" class="input-dark text-sm" required>
                                @error('title')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="original_title" class="block text-sm font-medium text-dark-200 mb-1.5">Tên
                                    gốc</label>
                                <input id="original_title" type="text" name="original_title"
                                    value="{{ old('original_title', $movie->original_title) }}"
                                    class="input-dark text-sm" placeholder="Tiêu đề gốc (tiếng Anh/ngôn ngữ gốc)">
                                @error('original_title')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="tagline"
                                    class="block text-sm font-medium text-dark-200 mb-1.5">Tagline</label>
                                <input id="tagline" type="text" name="tagline"
                                    value="{{ old('tagline', $movie->tagline) }}" class="input-dark text-sm"
                                    placeholder="Câu khẩu hiệu của phim">
                                @error('tagline')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="synopsis" class="block text-sm font-medium text-dark-200 mb-1.5">Mô tả / Nội
                                    dung</label>
                                <textarea id="synopsis" name="synopsis" rows="6" class="input-dark text-sm"
                                    placeholder="Tóm tắt nội dung phim...">{{ old('synopsis', $movie->synopsis) }}</textarea>
                                @error('synopsis')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Media --}}
                    <div class="card p-5">
                        <h3 class="text-sm font-semibold text-dark-300 uppercase tracking-wide mb-4">Media</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="poster" class="block text-sm font-medium text-dark-200 mb-1.5">Poster
                                    URL</label>
                                <input id="poster" type="url" name="poster"
                                    value="{{ old('poster', $movie->poster) }}" class="input-dark text-sm"
                                    placeholder="https://...">
                                @error('poster')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="backdrop" class="block text-sm font-medium text-dark-200 mb-1.5">Backdrop
                                    URL</label>
                                <input id="backdrop" type="url" name="backdrop"
                                    value="{{ old('backdrop', $movie->backdrop) }}" class="input-dark text-sm"
                                    placeholder="https://...">
                                @error('backdrop')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="trailer_url"
                                    class="block text-sm font-medium text-dark-200 mb-1.5">Trailer URL</label>
                                <input id="trailer_url" type="url" name="trailer_url"
                                    value="{{ old('trailer_url', $movie->trailer_url) }}" class="input-dark text-sm"
                                    placeholder="YouTube URL hoặc embed URL">
                                @error('trailer_url')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Thể loại --}}
                    <div class="card p-5">
                        <h3 class="text-sm font-semibold text-dark-300 uppercase tracking-wide mb-4">Thể loại</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($genres as $genre)
                                <label
                                    class="flex items-center gap-2 px-3 py-1.5 rounded-lg border cursor-pointer transition-colors
                                {{ in_array($genre->id, $selectedGenres) ? 'border-sky-500 bg-sky-600/20 text-sky-300' : 'border-dark-700 bg-dark-800 text-dark-300 hover:border-dark-500' }}">
                                    <input type="checkbox" name="genres[]" value="{{ $genre->id }}"
                                        {{ in_array($genre->id, $selectedGenres) ? 'checked' : '' }}
                                        class="w-3.5 h-3.5 rounded accent-sky-500">
                                    <span class="text-sm">{{ $genre->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('genres')
                            <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tài chính --}}
                    <div class="card p-5">
                        <h3 class="text-sm font-semibold text-dark-300 uppercase tracking-wide mb-4">Tài chính</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="budget" class="block text-sm font-medium text-dark-200 mb-1.5">Ngân sách
                                    (USD)</label>
                                <input id="budget" type="number" name="budget" min="0"
                                    value="{{ old('budget', $movie->budget) }}" class="input-dark text-sm"
                                    placeholder="0">
                                @error('budget')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="revenue" class="block text-sm font-medium text-dark-200 mb-1.5">Doanh thu
                                    (USD)</label>
                                <input id="revenue" type="number" name="revenue" min="0"
                                    value="{{ old('revenue', $movie->revenue) }}" class="input-dark text-sm"
                                    placeholder="0">
                                @error('revenue')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                </div>

                {{-- ── Cột phụ ────────────────────────────────────────────── --}}
                <div class="space-y-5">

                    {{-- Trạng thái & Kiểm duyệt --}}
                    <div class="card p-5">
                        <h3 class="text-sm font-semibold text-dark-300 uppercase tracking-wide mb-4">Trạng thái</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="status" class="block text-sm font-medium text-dark-200 mb-1.5">Hiển
                                    thị</label>
                                <select id="status" name="status" class="input-dark text-sm">
                                    <option value="active"
                                        {{ old('status', $movie->status) === 'active' ? 'selected' : '' }}>✅ Hoạt động
                                    </option>
                                    <option value="hidden"
                                        {{ old('status', $movie->status) === 'hidden' ? 'selected' : '' }}>🚫 Đã ẩn
                                    </option>
                                    <option value="upcoming"
                                        {{ old('status', $movie->status) === 'upcoming' ? 'selected' : '' }}>🕐 Sắp
                                        chiếu</option>
                                </select>
                            </div>
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input type="hidden" name="is_approved" value="0">
                                <input type="checkbox" name="is_approved" value="1"
                                    {{ old('is_approved', $movie->is_approved) ? 'checked' : '' }}
                                    class="w-4 h-4 rounded accent-sky-500">
                                <span class="text-sm text-dark-200">Đã duyệt (hiện trên trang)</span>
                            </label>
                        </div>
                    </div>

                    {{-- Thông tin phát hành --}}
                    <div class="card p-5">
                        <h3 class="text-sm font-semibold text-dark-300 uppercase tracking-wide mb-4">Phát hành</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="release_date" class="block text-sm font-medium text-dark-200 mb-1.5">Ngày
                                    phát hành</label>
                                <input id="release_date" type="date" name="release_date"
                                    value="{{ old('release_date', $movie->release_date?->format('Y-m-d')) }}"
                                    class="input-dark text-sm">
                            </div>
                            <div>
                                <label for="runtime" class="block text-sm font-medium text-dark-200 mb-1.5">Thời
                                    lượng (phút)</label>
                                <input id="runtime" type="number" name="runtime" min="0"
                                    value="{{ old('runtime', $movie->runtime) }}" class="input-dark text-sm"
                                    placeholder="90">
                            </div>
                            <x-admin.code-picker name="country" type="country" label="Quốc gia" :value="old('country', $movie->country ?? '')" />
                            <x-admin.code-picker name="language" type="language" label="Ngôn ngữ gốc"
                                :value="old('language', $movie->language ?? '')" />
                        </div>
                    </div>

                    {{-- Xóa phim --}}
                    <div class="card p-5 border border-red-500/20">
                        <h3 class="text-sm font-semibold text-red-400 uppercase tracking-wide mb-3">Vùng nguy hiểm</h3>
                        <p class="text-xs text-dark-400 mb-3">Xóa phim sẽ ẩn nó khỏi hệ thống!</p>
                        <button type="button"
                            @click="$dispatch('admin-confirm', { title: 'Xóa phim', message: 'Xóa phim \u00ab{{ addslashes($movie->title) }}\u00bb? Hành động này không thể hoàn tác.', formId: 'delete-movie-form' })"
                            class="w-full text-sm py-2 px-4 rounded-xl border border-red-500/40 text-red-400 hover:bg-red-500/10 transition-colors">
                            🗑 Xóa phim này
                        </button>
                    </div>

                    {{-- Hành động --}}
                    <div class="card p-3">
                        <button type="submit" class="btn-sky w-full text-sm py-2 font-semibold shadow-sm">
                            <svg class="w-4 h-4 mr-1.5 inline" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Lưu thay đổi
                        </button>
                    </div>

                </div>
            </div>

        </form>

        {{-- Delete form --}}
        <form id="delete-movie-form" action="{{ route('admin.movies.destroy', $movie) }}" method="POST"
            class="hidden">
            @csrf @method('DELETE')
        </form>

    </div>

</x-admin-layout>
