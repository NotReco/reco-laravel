<x-admin-layout :title="'Sửa — ' . $person->name" pageTitle="Sửa thông tin người">

    <div class="max-w-5xl">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-dark-500 mb-6">
            <a href="{{ route('admin.people.index') }}" class="hover:text-white transition-colors">Quản lý Diễn viên</a>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-dark-400 truncate">{{ $person->name }}</span>
        </nav>

        {{-- Header card --}}
        <div class="card p-5 mb-6 flex items-center gap-5">
            {{-- Avatar --}}
            <div class="w-20 h-28 rounded-xl bg-dark-800 overflow-hidden shrink-0 ring-1 ring-dark-700">
                @if ($person->photo)
                    <img src="{{ $person->photo }}" alt="{{ $person->name }}"
                        class="w-full h-full object-cover object-top">
                @else
                    <div class="w-full h-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-dark-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                @endif
            </div>

            <div class="flex-1 min-w-0">
                <h2 class="text-xl font-bold text-white">{{ $person->name }}</h2>
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1.5">
                    @if ($person->known_for)
                        <span class="text-xs px-2 py-0.5 rounded-full bg-sky-600/20 text-sky-400 font-medium">{{ $person->known_for }}</span>
                    @endif
                    @if ($person->gender_label)
                        <span class="text-xs text-dark-500">{{ $person->gender_label }}</span>
                    @endif
                    @if ($person->nationality)
                        <span class="text-xs text-dark-500">🌏 {{ $person->nationality }}</span>
                    @endif
                    @if ($person->date_of_birth)
                        <span class="text-xs text-dark-500">
                            🎂 {{ $person->date_of_birth->format('d/m/Y') }}
                            @if ($person->date_of_death)
                                → {{ $person->date_of_death->format('d/m/Y') }}
                            @endif
                        </span>
                    @endif
                </div>
                <div class="flex items-center gap-4 mt-2">
                    @if ($person->tmdb_id)
                        <span class="text-xs text-dark-500">TMDb: <span class="text-dark-300">{{ $person->tmdb_id }}</span></span>
                    @endif
                    <span class="text-xs text-dark-500">{{ $person->movies->count() }} bộ phim liên quan</span>
                </div>
            </div>

            <div class="ml-auto flex flex-col items-end gap-2 shrink-0">
                <a href="{{ route('person.show', $person) }}" target="_blank"
                    class="inline-flex items-center gap-1.5 text-sm text-dark-400 hover:text-sky-400 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    Xem trang
                </a>
                <a href="{{ route('admin.people.index') }}"
                    class="inline-flex items-center gap-1.5 text-sm text-dark-400 hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Quay lại
                </a>
            </div>
        </div>

        <form x-data="{ isDirty: false }" @input="isDirty = true" @change="isDirty = true"
            @reset="setTimeout(() => isDirty = false, 50)"
            action="{{ route('admin.people.update', $person) }}" method="POST">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- ── Cột chính ──────────────────────────────── --}}
                <div class="lg:col-span-2 space-y-5">

                    {{-- Thông tin cơ bản --}}
                    <div class="card p-5">
                        <h3 class="text-sm font-semibold text-dark-300 uppercase tracking-wide mb-4">Thông tin cơ bản</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-dark-200 mb-1.5">
                                    Tên <span class="text-red-400">*</span>
                                </label>
                                <input id="name" type="text" name="name"
                                    value="{{ old('name', $person->name) }}"
                                    class="input-dark text-sm" required>
                                @error('name')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="gender" class="block text-sm font-medium text-dark-200 mb-1.5">Giới tính</label>
                                    <select id="gender" name="gender" class="input-dark text-sm">
                                        <option value="0" {{ old('gender', $person->gender) == 0 ? 'selected' : '' }}>Không xác định</option>
                                        <option value="1" {{ old('gender', $person->gender) == 1 ? 'selected' : '' }}>Nữ</option>
                                        <option value="2" {{ old('gender', $person->gender) == 2 ? 'selected' : '' }}>Nam</option>
                                        <option value="3" {{ old('gender', $person->gender) == 3 ? 'selected' : '' }}>Phi nhị giới</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="known_for" class="block text-sm font-medium text-dark-200 mb-1.5">Nổi tiếng với</label>
                                    <select id="known_for" name="known_for" class="input-dark text-sm">
                                        <option value="">—</option>
                                        <option value="Acting"   {{ old('known_for', $person->known_for) === 'Acting'   ? 'selected' : '' }}>Diễn xuất</option>
                                        <option value="Directing" {{ old('known_for', $person->known_for) === 'Directing' ? 'selected' : '' }}>Đạo diễn</option>
                                        <option value="Writing"  {{ old('known_for', $person->known_for) === 'Writing'  ? 'selected' : '' }}>Biên kịch</option>
                                        <option value="Production" {{ old('known_for', $person->known_for) === 'Production' ? 'selected' : '' }}>Sản xuất</option>
                                        <option value="Crew"     {{ old('known_for', $person->known_for) === 'Crew'     ? 'selected' : '' }}>Đoàn phim</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label for="biography" class="block text-sm font-medium text-dark-200 mb-1.5">Tiểu sử</label>
                                <textarea id="biography" name="biography" rows="8"
                                    class="input-dark text-sm resize-y"
                                    placeholder="Tiểu sử chi tiết...">{{ old('biography', $person->biography ?? $person->bio) }}</textarea>
                                @error('biography')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Thông tin cá nhân --}}
                    <div class="card p-5">
                        <h3 class="text-sm font-semibold text-dark-300 uppercase tracking-wide mb-4">Thông tin cá nhân</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="date_of_birth" class="block text-sm font-medium text-dark-200 mb-1.5">Ngày sinh</label>
                                <input id="date_of_birth" type="date" name="date_of_birth"
                                    value="{{ old('date_of_birth', $person->date_of_birth?->format('Y-m-d')) }}"
                                    class="input-dark text-sm">
                                @error('date_of_birth')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="date_of_death" class="block text-sm font-medium text-dark-200 mb-1.5">Ngày mất</label>
                                <input id="date_of_death" type="date" name="date_of_death"
                                    value="{{ old('date_of_death', $person->date_of_death?->format('Y-m-d')) }}"
                                    class="input-dark text-sm">
                                @error('date_of_death')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="nationality" class="block text-sm font-medium text-dark-200 mb-1.5">Quốc tịch</label>
                                <input id="nationality" type="text" name="nationality"
                                    value="{{ old('nationality', $person->nationality) }}"
                                    class="input-dark text-sm" placeholder="American, Vietnamese, ...">
                            </div>
                            <div>
                                <label for="place_of_birth" class="block text-sm font-medium text-dark-200 mb-1.5">Nơi sinh</label>
                                <input id="place_of_birth" type="text" name="place_of_birth"
                                    value="{{ old('place_of_birth', $person->place_of_birth) }}"
                                    class="input-dark text-sm" placeholder="Los Angeles, California, USA">
                            </div>
                        </div>
                    </div>

                    {{-- Phim đã tham gia --}}
                    @if ($person->movies->isNotEmpty())
                        <div class="card p-5">
                            <h3 class="text-sm font-semibold text-dark-300 uppercase tracking-wide mb-4">
                                Phim đã tham gia
                                <span class="text-dark-500 font-normal normal-case">({{ $person->movies->count() }})</span>
                            </h3>
                            <div class="space-y-2 max-h-72 overflow-y-auto pr-1">
                                @foreach ($person->movies as $movie)
                                    <div class="flex items-center gap-3 py-2 px-3 rounded-xl bg-dark-800/50 hover:bg-dark-800 transition-colors group">
                                        @if ($movie->poster)
                                            <img src="{{ $movie->poster }}" alt="{{ $movie->title }}"
                                                class="w-8 h-11 rounded-lg object-cover shrink-0">
                                        @else
                                            <div class="w-8 h-11 rounded-lg bg-dark-700 shrink-0 flex items-center justify-center">
                                                <svg class="w-3 h-3 text-dark-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18" />
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <a href="{{ route('movies.show', $movie) }}" target="_blank"
                                                class="text-sm font-medium text-dark-200 group-hover:text-sky-400 transition-colors truncate block">
                                                {{ $movie->title }}
                                            </a>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                @php
                                                    $roleMap = [
                                                        'actor'    => ['Diễn viên', 'text-sky-400 bg-sky-500/10'],
                                                        'director' => ['Đạo diễn', 'text-amber-400 bg-amber-500/10'],
                                                        'writer'   => ['Biên kịch', 'text-emerald-400 bg-emerald-500/10'],
                                                        'producer' => ['Nhà sản xuất', 'text-purple-400 bg-purple-500/10'],
                                                    ];
                                                    [$roleLabel, $roleCls] = $roleMap[$movie->pivot->role] ?? [$movie->pivot->role, 'text-dark-400 bg-dark-800'];
                                                @endphp
                                                <span class="text-[10px] px-1.5 py-0.5 rounded {{ $roleCls }} font-medium">
                                                    {{ $roleLabel }}
                                                </span>
                                                @if ($movie->pivot->character_name)
                                                    <span class="text-xs text-dark-500 truncate italic">
                                                        vai {{ $movie->pivot->character_name }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        @if ($movie->release_date)
                                            <span class="text-xs text-dark-600 shrink-0 tabular-nums">
                                                {{ $movie->release_date->format('Y') }}
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>

                {{-- ── Cột phụ ──────────────────────────────────── --}}
                <div class="space-y-5">

                    {{-- Ảnh & URL --}}
                    <div class="card p-5">
                        <h3 class="text-sm font-semibold text-dark-300 uppercase tracking-wide mb-4">Ảnh đại diện</h3>
                        @if ($person->photo)
                            <div class="mb-3 rounded-xl overflow-hidden aspect-[2/3] bg-dark-800">
                                <img src="{{ old('photo', $person->photo) }}" alt=""
                                    class="w-full h-full object-cover object-top" id="photo-preview">
                            </div>
                        @else
                            <div class="mb-3 rounded-xl overflow-hidden aspect-[2/3] bg-dark-800 flex items-center justify-center" id="photo-preview-wrap">
                                <svg class="w-10 h-10 text-dark-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                        @endif
                        <div>
                            <label for="photo" class="block text-sm font-medium text-dark-200 mb-1.5">URL ảnh</label>
                            <input id="photo" type="url" name="photo"
                                value="{{ old('photo', $person->photo) }}"
                                class="input-dark text-sm" placeholder="https://image.tmdb.org/...">
                            @error('photo')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Mạng xã hội & liên kết --}}
                    <div class="card p-5">
                        <h3 class="text-sm font-semibold text-dark-300 uppercase tracking-wide mb-4">Liên kết & Mạng xã hội</h3>
                        <div class="space-y-3">
                            <div>
                                <label for="imdb_id" class="block text-sm font-medium text-dark-200 mb-1.5">
                                    <span class="text-amber-400">IMDb</span> ID
                                </label>
                                <input id="imdb_id" type="text" name="imdb_id"
                                    value="{{ old('imdb_id', $person->imdb_id) }}"
                                    class="input-dark text-sm font-mono" placeholder="nm0000123">
                            </div>
                            <div>
                                <label for="instagram_id" class="block text-sm font-medium text-dark-200 mb-1.5">
                                    <span class="text-pink-400">Instagram</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-dark-500 text-sm">@</span>
                                    <input id="instagram_id" type="text" name="instagram_id"
                                        value="{{ old('instagram_id', $person->instagram_id) }}"
                                        class="input-dark text-sm pl-7" placeholder="username">
                                </div>
                            </div>
                            <div>
                                <label for="twitter_id" class="block text-sm font-medium text-dark-200 mb-1.5">
                                    <span class="text-sky-400">Twitter / X</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-dark-500 text-sm">@</span>
                                    <input id="twitter_id" type="text" name="twitter_id"
                                        value="{{ old('twitter_id', $person->twitter_id) }}"
                                        class="input-dark text-sm pl-7" placeholder="username">
                                </div>
                            </div>
                            <div>
                                <label for="homepage" class="block text-sm font-medium text-dark-200 mb-1.5">Website</label>
                                <input id="homepage" type="url" name="homepage"
                                    value="{{ old('homepage', $person->homepage) }}"
                                    class="input-dark text-sm" placeholder="https://...">
                            </div>
                        </div>
                    </div>

                    {{-- Hành động --}}
                    <div class="card p-3">
                        <button type="submit" class="btn-sky w-full text-sm py-2 font-semibold shadow-sm">
                            <svg class="w-4 h-4 mr-1.5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Lưu thay đổi
                        </button>
                    </div>

                    {{-- Bỏ thay đổi --}}
                    <div class="card p-3" x-show="isDirty" x-cloak x-transition>
                        <button type="reset"
                            class="w-full text-sm py-2 font-semibold bg-dark-800 text-dark-300 hover:bg-dark-700 rounded-lg transition-colors border border-dark-700">
                            Hủy thay đổi
                        </button>
                    </div>

                    {{-- Vùng nguy hiểm --}}
                    <div class="card p-5 border border-red-500/20">
                        <h3 class="text-sm font-semibold text-red-400 uppercase tracking-wide mb-3">Vùng nguy hiểm</h3>
                        <p class="text-xs text-dark-400 mb-3">Xóa người này khỏi hệ thống (soft delete).</p>
                        <button type="button"
                            @click="$dispatch('admin-confirm', { title: 'Xóa người này', message: 'Xóa «{{ addslashes($person->name) }}»? Hành động này không thể hoàn tác.', formId: 'delete-person-form' })"
                            class="w-full text-sm py-2 px-4 rounded-xl border border-red-500/40 text-red-400 hover:bg-red-500/10 transition-colors">
                            🗑 Xóa khỏi hệ thống
                        </button>
                    </div>

                </div>
            </div>
        </form>

        {{-- Delete form --}}
        <form id="delete-person-form" action="{{ route('admin.people.destroy', $person) }}" method="POST" class="hidden">
            @csrf @method('DELETE')
        </form>
    </div>

</x-admin-layout>
