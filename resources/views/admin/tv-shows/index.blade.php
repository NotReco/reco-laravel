<x-admin-layout :title="'TV Series'" pageTitle="Quản lý TV Series">

    {{-- ── Toolbar ───────────────────────────────────────────────── --}}
    <div class="flex items-center gap-3 mb-6">
        <form action="{{ route('admin.tv-shows.index') }}" method="GET" class="flex gap-2 flex-1 max-w-md">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-dark-500 pointer-events-none"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0" />
                </svg>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm theo tên series..."
                    class="input-dark text-sm pl-9 py-2.5 w-full" autocomplete="off">
            </div>
            <button type="submit" class="btn-secondary py-2.5 px-4 text-sm shrink-0">Tìm</button>
            @if (request('q'))
                <a href="{{ route('admin.tv-shows.index') }}"
                    class="py-2.5 px-3 text-sm text-dark-400 hover:text-white transition-colors shrink-0">✕ Xóa lọc</a>
            @endif
        </form>
        <div class="ml-auto flex items-center gap-2 shrink-0 px-3 py-1.5 rounded-xl bg-sky-600/15 border border-sky-500/30">
            <svg class="w-3.5 h-3.5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            <span class="text-sm font-semibold text-sky-300">{{ $tvShows->total() }}</span>
            <span class="text-xs text-sky-400/70">series</span>
        </div>
    </div>

    {{-- ── Table ─────────────────────────────────────────────────── --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-dark-700 text-dark-400 text-left text-xs uppercase tracking-wide">
                        <th class="px-5 py-3 font-medium">TV Series</th>
                        <th class="px-5 py-3 font-medium">Thể loại</th>
                        <th class="px-5 py-3 font-medium">Năm</th>
                        <th class="px-5 py-3 font-medium">Trạng thái</th>
                        <th class="px-5 py-3 font-medium">Đánh giá</th>
                        <th class="px-5 py-3 font-medium text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-dark-800/60">
                    @forelse($tvShows as $tvShow)
                        <tr class="hover:bg-dark-800/40 transition-colors group">
                            {{-- Tên series --}}
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-14 rounded-lg bg-dark-700 bg-cover bg-center shrink-0 ring-1 ring-dark-700"
                                        style="background-image: url('{{ $tvShow->poster }}')">
                                        @if (!$tvShow->poster)
                                            <div class="w-full h-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-dark-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="1.5"
                                                        d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <a href="{{ route('tv-shows.show', $tvShow) }}"
                                            class="font-medium text-white hover:text-sky-400 transition-colors truncate block max-w-xs"
                                            target="_blank">
                                            {{ $tvShow->title }}
                                        </a>
                                        @if ($tvShow->original_title && $tvShow->original_title !== $tvShow->title)
                                            <p class="text-xs text-dark-500 truncate max-w-xs">
                                                {{ $tvShow->original_title }}</p>
                                        @endif
                                        <div class="flex items-center gap-1.5 mt-0.5">
                                            @if (!$tvShow->is_approved)
                                                <span
                                                    class="text-[10px] px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-400 font-medium">Chưa
                                                    duyệt</span>
                                            @endif
                                            @if ($tvShow->number_of_seasons)
                                                <span class="text-[10px] text-dark-500">{{ $tvShow->number_of_seasons }}
                                                    mùa</span>
                                            @endif
                                            @if ($tvShow->number_of_episodes)
                                                <span class="text-[10px] text-dark-600">·
                                                    {{ $tvShow->number_of_episodes }} tập</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Thể loại --}}
                            <td class="px-5 py-3">
                                <div class="flex flex-wrap gap-1 max-w-[180px]">
                                    @foreach ($tvShow->genres->take(3) as $genre)
                                        <span
                                            class="text-[10px] px-1.5 py-0.5 rounded bg-dark-700 text-dark-300">{{ $genre->name }}</span>
                                    @endforeach
                                    @if ($tvShow->genres->count() > 3)
                                        <span
                                            class="text-[10px] text-dark-500">+{{ $tvShow->genres->count() - 3 }}</span>
                                    @endif
                                    @if ($tvShow->genres->isEmpty())
                                        <span class="text-xs text-dark-600">—</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Năm --}}
                            <td class="px-5 py-3 text-dark-400 tabular-nums">
                                {{ $tvShow->first_air_date ? $tvShow->first_air_date->format('Y') : '—' }}
                            </td>

                            {{-- Trạng thái --}}
                            <td class="px-5 py-3">
                                @php
                                    $statusMap = [
                                        'active' => ['Hoạt động', 'text-emerald-400 bg-emerald-500/10'],
                                        'hidden' => ['Đã ẩn', 'text-dark-400 bg-dark-800'],
                                        'upcoming' => ['Sắp chiếu', 'text-amber-400 bg-amber-500/10'],
                                    ];
                                    [$statusLabel, $statusCls] = $statusMap[$tvShow->status] ?? ['?', ''];
                                @endphp
                                <span
                                    class="text-[11px] px-2 py-0.5 rounded-full font-medium {{ $statusCls }}">{{ $statusLabel }}</span>
                            </td>

                            {{-- Đánh giá --}}
                            <td class="px-5 py-3">
                                @if ($tvShow->reviews_avg_rating)
                                    <div class="flex items-center gap-1">
                                        <span
                                            class="font-semibold text-amber-400">{{ number_format($tvShow->reviews_avg_rating, 1) }}</span>
                                        <span class="text-xs text-dark-500">({{ $tvShow->reviews_count }})</span>
                                    </div>
                                @else
                                    <span class="text-dark-600">—</span>
                                @endif
                            </td>

                            {{-- Thao tác --}}
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('tv-shows.show', $tvShow) }}" target="_blank"
                                        class="p-1.5 rounded-lg text-dark-500 hover:text-sky-400 hover:bg-dark-800 transition-colors"
                                        title="Xem trang">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.tv-shows.edit', $tvShow) }}"
                                        class="p-1.5 rounded-lg text-dark-500 hover:text-white hover:bg-dark-800 transition-colors"
                                        title="Sửa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <button type="button"
                                        @click="$dispatch('admin-confirm', { title: 'Xóa TV Series', message: 'Xóa \u00ab{{ addslashes($tvShow->title) }}\u00bb? Hành động này không thể hoàn tác.', formId: 'del-tvshow-{{ $tvShow->id }}' })"
                                        class="p-1.5 rounded-lg text-dark-500 hover:text-red-400 hover:bg-dark-800 transition-colors"
                                        title="Xóa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                    <form id="del-tvshow-{{ $tvShow->id }}"
                                        action="{{ route('admin.tv-shows.destroy', $tvShow) }}" method="POST"
                                        class="hidden">
                                        @csrf @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center gap-3 text-dark-500">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                            d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <p>Không tìm thấy TV Series
                                        nào{{ request('q') ? ' cho "' . request('q') . '"' : '' }}.</p>
                                </div>
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
