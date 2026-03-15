<x-admin-layout :title="'Sửa phim — ' . $movie->title" pageTitle="Sửa phim">

<div class="max-w-3xl">
    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-dark-500 mb-6">
        <a href="{{ route('admin.movies.index') }}" class="hover:text-white transition-colors">Phim</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-dark-400 truncate">{{ $movie->title }}</span>
    </nav>

    <div class="card p-6 sm:p-8">
        <div class="flex items-center gap-4 mb-6">
            @if($movie->poster)
                <div class="w-16 h-24 rounded-lg bg-dark-700 bg-cover bg-center shrink-0"
                     style="background-image: url('{{ $movie->poster }}')"></div>
            @endif
            <div>
                <h2 class="text-xl font-bold text-white">{{ $movie->title }}</h2>
                @if($movie->original_title && $movie->original_title !== $movie->title)
                    <p class="text-sm text-dark-400">{{ $movie->original_title }}</p>
                @endif
            </div>
        </div>

        <form action="{{ route('admin.movies.update', $movie) }}" method="POST" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label for="title" class="block text-sm font-medium text-dark-200 mb-2">Tên phim</label>
                <input id="title" type="text" name="title" value="{{ old('title', $movie->title) }}"
                       class="input-dark text-sm" required>
                @error('title') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="overview" class="block text-sm font-medium text-dark-200 mb-2">Mô tả</label>
                <textarea id="overview" name="overview" rows="5"
                          class="input-dark text-sm resize-none">{{ old('overview', $movie->overview) }}</textarea>
                @error('overview') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="release_date" class="block text-sm font-medium text-dark-200 mb-2">Ngày phát hành</label>
                    <input id="release_date" type="date" name="release_date"
                           value="{{ old('release_date', $movie->release_date?->format('Y-m-d')) }}"
                           class="input-dark text-sm">
                </div>
                <div>
                    <label for="runtime" class="block text-sm font-medium text-dark-200 mb-2">Thời lượng (phút)</label>
                    <input id="runtime" type="number" name="runtime" min="0"
                           value="{{ old('runtime', $movie->runtime) }}"
                           class="input-dark text-sm">
                </div>
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-dark-200 mb-2">Trạng thái</label>
                <select id="status" name="status" class="input-dark text-sm">
                    @foreach(['Released', 'Post Production', 'In Production', 'Planned', 'Canceled'] as $s)
                        <option value="{{ $s }}" {{ old('status', $movie->status) === $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-dark-700">
                <a href="{{ route('admin.movies.index') }}" class="text-sm text-dark-400 hover:text-white transition-colors">← Quay lại</a>
                <button type="submit" class="btn-rose text-sm py-2.5 px-6">Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>

</x-admin-layout>
