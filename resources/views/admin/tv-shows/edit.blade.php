<x-admin-layout :title="'Sửa TV Series — ' . $tvShow->title" pageTitle="Sửa TV Series">

<div class="max-w-3xl">
    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-dark-500 mb-6">
        <a href="{{ route('admin.tv-shows.index') }}" class="hover:text-white transition-colors">TV Series</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-dark-400 truncate">{{ $tvShow->title }}</span>
    </nav>

    <div class="card p-6 sm:p-8">
        <div class="flex items-center gap-4 mb-6">
            @if($tvShow->poster)
                <div class="w-16 h-24 rounded-lg bg-dark-700 bg-cover bg-center shrink-0"
                     style="background-image: url('{{ $tvShow->poster }}')"></div>
            @endif
            <div>
                <h2 class="text-xl font-bold text-white">{{ $tvShow->title }}</h2>
                @if($tvShow->original_title && $tvShow->original_title !== $tvShow->title)
                    <p class="text-sm text-dark-400">{{ $tvShow->original_title }}</p>
                @endif
            </div>
        </div>

        <form action="{{ route('admin.tv-shows.update', $tvShow) }}" method="POST" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label for="title" class="block text-sm font-medium text-dark-200 mb-2">Tên TV Series</label>
                <input id="title" type="text" name="title" value="{{ old('title', $tvShow->title) }}"
                       class="input-dark text-sm" required>
                @error('title') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="synopsis" class="block text-sm font-medium text-dark-200 mb-2">Mô tả (Synopsis)</label>
                <textarea id="synopsis" name="synopsis" rows="5"
                          class="js-richtext input-dark text-sm resize-none"
                          data-richtext-height="320">{{ old('synopsis', $tvShow->synopsis) }}</textarea>
                @error('synopsis') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="first_air_date" class="block text-sm font-medium text-dark-200 mb-2">Ngày phát sóng</label>
                    <input id="first_air_date" type="date" name="first_air_date"
                           value="{{ old('first_air_date', $tvShow->first_air_date?->format('Y-m-d')) }}"
                           class="input-dark text-sm">
                </div>
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-dark-200 mb-2">Trạng thái</label>
                <select id="status" name="status" class="input-dark text-sm">
                    @foreach([
                        'active' => 'Hoạt động',
                        'hidden' => 'Đã ẩn',
                        'upcoming' => 'Sắp chiếu',
                    ] as $val => $label)
                        <option value="{{ $val }}" {{ old('status', $tvShow->status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-dark-700">
                <a href="{{ route('admin.tv-shows.index') }}" class="text-sm text-dark-400 hover:text-white transition-colors">← Quay lại</a>
                <button type="submit" class="btn-sky text-sm py-2.5 px-6">Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>

</x-admin-layout>
