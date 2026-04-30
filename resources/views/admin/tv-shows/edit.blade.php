<x-admin-layout :title="'Sửa TV Series — ' . $tvShow->title" pageTitle="Sửa TV Series">

    <div class="max-w-4xl">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-dark-500 mb-6">
            <a href="{{ route('admin.tv-shows.index') }}" class="hover:text-white transition-colors">Quản lý TV Series</a>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-dark-400 truncate">{{ $tvShow->title }}</span>
        </nav>

        {{-- Header card --}}
        <div class="card p-5 mb-6 flex items-center gap-4">
            @if ($tvShow->poster)
                <div class="w-16 h-24 rounded-lg bg-dark-700 bg-cover bg-center shrink-0"
                    style="background-image: url('{{ $tvShow->poster }}')"></div>
            @else
                <div class="w-16 h-24 rounded-lg bg-dark-700 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-dark-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
            @endif
            <div>
                <h2 class="text-xl font-bold text-white">{{ $tvShow->title }}</h2>
                @if ($tvShow->original_title && $tvShow->original_title !== $tvShow->title)
                    <p class="text-sm text-dark-400">{{ $tvShow->original_title }}</p>
                @endif
                <div class="flex items-center gap-3 mt-2">
                    <span class="text-xs text-dark-500">TMDb ID: <span
                            class="text-dark-300">{{ $tvShow->tmdb_id ?? '—' }}</span></span>
                    <span class="text-xs text-dark-500">Tạo lúc: <span
                            class="text-dark-300">{{ $tvShow->created_at->format('d/m/Y') }}</span></span>
                </div>
            </div>
            <div class="ml-auto flex flex-col items-end gap-2">
                <a href="{{ route('tv-shows.show', $tvShow) }}" target="_blank"
                    class="inline-flex items-center gap-1.5 text-sm text-dark-400 hover:text-sky-400 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    Xem trang
                </a>
                <a href="{{ route('admin.tv-shows.index') }}"
                    class="inline-flex items-center gap-1.5 text-sm text-dark-400 hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Quay lại
                </a>
            </div>
        </div>

        <form x-data="{ isDirty: false }" @input="isDirty = true" @change="isDirty = true" @reset="setTimeout(() => isDirty = false, 50)" action="{{ route('admin.tv-shows.update', $tvShow) }}" method="POST">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- ── Cột chính ─────────────────────────────────────────── --}}
                <div class="lg:col-span-2 space-y-5">

                    {{-- Thông tin cơ bản --}}
                    <div class="card p-5">
                        <h3 class="text-sm font-semibold text-dark-300 uppercase tracking-wide mb-4">Thông tin cơ bản
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <label for="title" class="block text-sm font-medium text-dark-200 mb-1.5">Tên TV
                                    Series <span class="text-red-400">*</span></label>
                                <input id="title" type="text" name="title"
                                    value="{{ old('title', $tvShow->title) }}" class="input-dark text-sm" required>
                                @error('title')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="original_title" class="block text-sm font-medium text-dark-200 mb-1.5">Tên
                                    gốc</label>
                                <input id="original_title" type="text" name="original_title"
                                    value="{{ old('original_title', $tvShow->original_title) }}"
                                    class="input-dark text-sm" placeholder="Tiêu đề gốc">
                                @error('original_title')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="tagline"
                                    class="block text-sm font-medium text-dark-200 mb-1.5">Tagline</label>
                                <input id="tagline" type="text" name="tagline"
                                    value="{{ old('tagline', $tvShow->tagline) }}" class="input-dark text-sm"
                                    placeholder="Câu khẩu hiệu">
                                @error('tagline')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="synopsis" class="block text-sm font-medium text-dark-200 mb-1.5">Mô tả / Nội
                                    dung</label>
                                <textarea id="synopsis" name="synopsis" rows="6" class="input-dark text-sm" placeholder="Tóm tắt nội dung...">{{ old('synopsis', $tvShow->synopsis) }}</textarea>
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
                                    value="{{ old('poster', $tvShow->poster) }}" class="input-dark text-sm"
                                    placeholder="https://...">
                                @error('poster')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="backdrop" class="block text-sm font-medium text-dark-200 mb-1.5">Backdrop
                                    URL</label>
                                <input id="backdrop" type="url" name="backdrop"
                                    value="{{ old('backdrop', $tvShow->backdrop) }}" class="input-dark text-sm"
                                    placeholder="https://...">
                                @error('backdrop')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="trailer_url"
                                    class="block text-sm font-medium text-dark-200 mb-1.5">Trailer URL</label>
                                <input id="trailer_url" type="url" name="trailer_url"
                                    value="{{ old('trailer_url', $tvShow->trailer_url) }}" class="input-dark text-sm"
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

                    {{-- Thông số series --}}
                    <div class="card p-5">
                        <h3 class="text-sm font-semibold text-dark-300 uppercase tracking-wide mb-4">Thông số series
                        </h3>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label for="number_of_seasons"
                                    class="block text-sm font-medium text-dark-200 mb-1.5">Số mùa</label>
                                <input id="number_of_seasons" type="number" name="number_of_seasons" min="0"
                                    value="{{ old('number_of_seasons', $tvShow->number_of_seasons) }}"
                                    class="input-dark text-sm" placeholder="1">
                            </div>
                            <div>
                                <label for="number_of_episodes"
                                    class="block text-sm font-medium text-dark-200 mb-1.5">Số tập</label>
                                <input id="number_of_episodes" type="number" name="number_of_episodes"
                                    min="0"
                                    value="{{ old('number_of_episodes', $tvShow->number_of_episodes) }}"
                                    class="input-dark text-sm" placeholder="12">
                            </div>
                            <div>
                                <label for="episode_runtime"
                                    class="block text-sm font-medium text-dark-200 mb-1.5">Thời lượng/tập
                                    (phút)</label>
                                <input id="episode_runtime" type="number" name="episode_runtime" min="0"
                                    value="{{ old('episode_runtime', $tvShow->episode_runtime) }}"
                                    class="input-dark text-sm" placeholder="45">
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
                            <x-admin.code-picker name="status" type="status" label="Hiển thị" :value="old('status', $tvShow->status)" />
                            <x-admin.code-picker name="tmdb_status" type="tmdb_status" label="Trạng thái TMDb" :value="old('tmdb_status', $tvShow->tmdb_status)" />
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input type="hidden" name="is_approved" value="0">
                                <input type="checkbox" name="is_approved" value="1"
                                    {{ old('is_approved', $tvShow->is_approved) ? 'checked' : '' }}
                                    class="w-4 h-4 rounded accent-sky-500">
                                <span class="text-sm text-dark-200">Đã duyệt (hiện trên trang)</span>
                            </label>
                        </div>
                    </div>

                    {{-- Thông tin phát sóng --}}
                    <div class="card p-5">
                        <h3 class="text-sm font-semibold text-dark-300 uppercase tracking-wide mb-4">Phát sóng</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="first_air_date"
                                    class="block text-sm font-medium text-dark-200 mb-1.5">Ngày phát sóng đầu</label>
                                <input id="first_air_date" type="date" name="first_air_date"
                                    value="{{ old('first_air_date', $tvShow->first_air_date?->format('Y-m-d')) }}"
                                    class="input-dark text-sm">
                            </div>
                            <div>
                                <label for="last_air_date" class="block text-sm font-medium text-dark-200 mb-1.5">Ngày
                                    phát sóng cuối</label>
                                <input id="last_air_date" type="date" name="last_air_date"
                                    value="{{ old('last_air_date', $tvShow->last_air_date?->format('Y-m-d')) }}"
                                    class="input-dark text-sm">
                            </div>
                            <x-admin.code-picker name="country" type="country" label="Quốc gia" :value="old('country', $tvShow->country ?? '')" />
                            <x-admin.code-picker name="language" type="language" label="Ngôn ngữ gốc"
                                :value="old('language', $tvShow->language ?? '')" />
                            <x-admin.code-picker name="type" type="tv_type" label="Loại" :value="old('type', $tvShow->type ?? '')" />
                        </div>
                    </div>

                    {{-- Xóa --}}
                    <div class="card p-5 border border-red-500/20">
                        <h3 class="text-sm font-semibold text-red-400 uppercase tracking-wide mb-3">Vùng nguy hiểm</h3>
                        <p class="text-xs text-dark-400 mb-3">Xóa series sẽ ẩn nó khỏi hệ thống!</p>
                        <button type="button"
                            @click="$dispatch('admin-confirm', { title: 'Xóa TV Series', message: 'Xóa \u00ab{{ addslashes($tvShow->title) }}\u00bb? Hành động này không thể hoàn tác.', formId: 'delete-tvshow-form' })"
                            class="w-full text-sm py-2 px-4 rounded-xl border border-red-500/40 text-red-400 hover:bg-red-500/10 transition-colors">
                            🗑 Xóa series này
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

                    {{-- Bỏ thay đổi --}}
                    <div class="card p-3" x-show="isDirty" x-cloak x-transition>
                        <button type="reset" class="w-full text-sm py-2 font-semibold bg-red-500 text-white hover:bg-red-600 rounded-lg transition-colors">
                            Bỏ thay đổi
                        </button>
                    </div>

                </div>
            </div>

        </form>

        {{-- Delete form --}}
        <form id="delete-tvshow-form" action="{{ route('admin.tv-shows.destroy', $tvShow) }}" method="POST"
            class="hidden">
            @csrf @method('DELETE')
        </form>

    </div>

</x-admin-layout>
