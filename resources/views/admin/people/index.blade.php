<x-admin-layout :title="'Diễn viên'" pageTitle="Quản lý Diễn viên">

    {{-- ── Toolbar ───────────────────────────────────────────────── --}}
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <form action="{{ route('admin.people.index') }}" method="GET" class="flex gap-2 flex-1 max-w-lg">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-dark-500 pointer-events-none"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0" />
                </svg>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm theo tên..."
                    class="input-dark text-sm pl-9 py-2.5 w-full" autocomplete="off">
            </div>

            {{-- Filter vai trò --}}
            <select name="role" class="input-dark text-sm w-44 py-2.5 pr-8 shrink-0">
                <option value="">Tất cả vai trò</option>
                <option value="actor"    {{ request('role') === 'actor'    ? 'selected' : '' }}>Diễn viên</option>
                <option value="director" {{ request('role') === 'director' ? 'selected' : '' }}>Đạo diễn</option>
                <option value="writer"   {{ request('role') === 'writer'   ? 'selected' : '' }}>Biên kịch</option>
                <option value="producer" {{ request('role') === 'producer' ? 'selected' : '' }}>Nhà sản xuất</option>
            </select>

            <button type="submit" class="btn-secondary py-2.5 px-4 text-sm shrink-0">Tìm</button>
            @if (request('q') || request('role'))
                <a href="{{ route('admin.people.index') }}"
                    class="py-2.5 px-3 text-sm text-dark-400 hover:text-white transition-colors shrink-0">✕ Xóa lọc</a>
            @endif
        </form>

        {{-- Counter --}}
        <div class="ml-auto flex items-center gap-2 shrink-0 px-3 py-1.5 rounded-xl bg-sky-600/15 border border-sky-500/30">
            <svg class="w-3.5 h-3.5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span class="text-sm font-semibold text-sky-300">{{ $people->total() }}</span>
            <span class="text-xs text-sky-400/70">người</span>
        </div>
    </div>

    {{-- ── Grid ──────────────────────────────────────────────────── --}}
    @if ($people->isEmpty())
        <div class="card flex flex-col items-center justify-center py-24 text-dark-500 gap-3">
            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <p>Không tìm thấy diễn viên nào{{ request('q') ? ' cho "' . request('q') . '"' : '' }}.</p>
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
            @foreach ($people as $person)
                <div class="card group relative overflow-hidden hover:ring-1 hover:ring-sky-500/50 transition-all">
                    {{-- Photo --}}
                    <div class="aspect-[2/3] bg-dark-800 relative overflow-hidden">
                        @if ($person->photo)
                            <img src="{{ $person->photo }}" alt="{{ $person->name }}"
                                class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-10 h-10 text-dark-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                        @endif

                        {{-- Hover overlay --}}
                        <div class="absolute inset-0 bg-dark-950/70 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                            <a href="{{ route('admin.people.edit', $person) }}"
                                class="w-9 h-9 rounded-xl bg-dark-800 border border-dark-600 flex items-center justify-center text-dark-200 hover:text-white hover:border-sky-500 transition-all"
                                title="Sửa thông tin">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            <a href="{{ route('person.show', $person) }}" target="_blank"
                                class="w-9 h-9 rounded-xl bg-dark-800 border border-dark-600 flex items-center justify-center text-dark-200 hover:text-sky-400 hover:border-sky-500 transition-all"
                                title="Xem trang công khai">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </a>
                            <button type="button"
                                @click="$dispatch('admin-confirm', { title: 'Xóa người này', message: 'Xóa «{{ addslashes($person->name) }}»? Hành động này không thể hoàn tác.', formId: 'del-person-{{ $person->id }}' })"
                                class="w-9 h-9 rounded-xl bg-dark-800 border border-dark-600 flex items-center justify-center text-dark-200 hover:text-red-400 hover:border-red-500 transition-all"
                                title="Xóa">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                            <form id="del-person-{{ $person->id }}" action="{{ route('admin.people.destroy', $person) }}"
                                method="POST" class="hidden">
                                @csrf @method('DELETE')
                            </form>
                        </div>

                        {{-- known_for badge --}}
                        @if ($person->known_for)
                            <div class="absolute top-1.5 left-1.5">
                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-md bg-dark-900/80 text-dark-300 uppercase tracking-wider">
                                    {{ $person->known_for }}
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="p-2.5">
                        <p class="text-sm font-semibold text-white truncate leading-tight">{{ $person->name }}</p>
                        <div class="flex items-center justify-between mt-1">
                            @if ($person->date_of_birth)
                                <span class="text-[11px] text-dark-500">
                                    {{ $person->date_of_birth->format('Y') }}
                                    @if ($person->date_of_death)
                                        – {{ $person->date_of_death->format('Y') }}
                                    @endif
                                </span>
                            @endif
                            <span class="text-[11px] text-dark-500 ml-auto">{{ $person->movies_count }} phim</span>
                        </div>
                        @if ($person->nationality)
                            <p class="text-[11px] text-dark-600 mt-0.5 truncate">{{ $person->nationality }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $people->links() }}
        </div>
    @endif

</x-admin-layout>
