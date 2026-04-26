<x-admin-layout :title="'TV Series'" pageTitle="Quản lý TV Series">

    {{-- ── Search ────────────────────────────────────────────────── --}}
    <div class="mb-6">
        <form action="{{ route('admin.tv-shows.index') }}" method="GET" class="flex gap-3 max-w-lg">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm kiếm TV Series..."
                class="input-dark text-sm flex-1 py-2.5">
            <button type="submit" class="btn-secondary py-2.5 px-5 text-sm">Tìm</button>
        </form>
    </div>

    {{-- ── Table ─────────────────────────────────────────────────── --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-dark-700 text-dark-400 text-left">
                        <th class="px-5 py-3 font-medium">TV Series</th>
                        <th class="px-5 py-3 font-medium">Năm</th>
                        <th class="px-5 py-3 font-medium">Đánh giá</th>
                        <th class="px-5 py-3 font-medium">Reviews</th>
                        <th class="px-5 py-3 font-medium text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-dark-800">
                    @forelse($tvShows as $tvShow)
                        <tr class="hover:bg-dark-800/30 transition-colors">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-14 rounded bg-dark-700 bg-cover bg-center shrink-0"
                                        style="background-image: url('{{ $tvShow->poster }}')"></div>
                                    <div class="min-w-0">
                                        <a href="{{ route('tv-shows.show', $tvShow) }}"
                                            class="font-medium text-white hover:text-sky-400 transition-colors truncate block"
                                            target="_blank">
                                            {{ $tvShow->title }}
                                        </a>
                                        @if ($tvShow->original_title && $tvShow->original_title !== $tvShow->title)
                                            <p class="text-xs text-dark-500 truncate">{{ $tvShow->original_title }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-dark-400">
                                {{ $tvShow->first_air_date ? $tvShow->first_air_date->format('Y') : '—' }}</td>
                            <td class="px-5 py-3">
                                @if ($tvShow->reviews_avg_rating)
                                    <span
                                        class="font-semibold text-amber-400">{{ number_format($tvShow->reviews_avg_rating, 1) }}</span>
                                @else
                                    <span class="text-dark-600">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-dark-400">{{ $tvShow->reviews_count }}</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.tv-shows.edit', $tvShow) }}"
                                        class="text-dark-400 hover:text-white transition-colors p-1" title="Sửa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.tv-shows.destroy', $tvShow) }}" method="POST"
                                        onsubmit="return confirm('Xóa TV Series «{{ $tvShow->title }}»?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="text-dark-400 hover:text-red-400 transition-colors p-1"
                                            title="Xóa">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-dark-500">Không tìm thấy phim nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $tvShows->links() }}
    </div>

</x-admin-layout>
