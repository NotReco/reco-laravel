<x-app-layout>
    <x-slot:title>Diễn viên & Đạo diễn</x-slot:title>

    <div
        x-data="{
            q: '{{ request('q') }}',
            role: '{{ request('known_for') }}',
            items: [],
            total: 0,
            loading: false,
            page: 1,
            hasMore: false,
            abortCtrl: null,
            roleMap: {
                '': 'Tất cả',
                'Acting': 'Diễn viên',
                'Directing': 'Đạo diễn',
                'Writing': 'Biên kịch',
                'Production': 'Sản xuất',
                'Camera': 'Quay phim',
                'Editing': 'Biên tập',
            },

            async fetch(reset = true) {
                if (reset) { this.page = 1; this.items = []; }
                if (this.abortCtrl) this.abortCtrl.abort();
                this.abortCtrl = new AbortController();
                this.loading = true;

                const params = new URLSearchParams({ page: this.page });
                if (this.q.trim()) params.set('q', this.q.trim());
                if (this.role) params.set('known_for', this.role);

                try {
                    const res = await fetch('{{ route('person.index') }}?' + params.toString(), {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        signal: this.abortCtrl.signal,
                    });
                    const data = await res.json();
                    this.items = reset ? data.items : [...this.items, ...data.items];
                    this.total = data.total;
                    this.hasMore = data.has_more;
                    if (!reset) this.page++;
                } catch (e) { if (e.name !== 'AbortError') console.error(e); }

                this.loading = false;
            },

            loadMore() {
                this.page++;
                this.fetch(false);
            },

            setRole(val) {
                this.role = val;
                this.fetch();
            },

            init() { this.fetch(); }
        }"
    >
        {{-- ── Header ── --}}
        <div class="bg-white border-b border-gray-100 pt-6 pb-0">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6">
                    <div>
                        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Diễn viên & Đạo diễn</h1>
                    </div>

                    {{-- Search box (AJAX, no submit button, no autocomplete history) --}}
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input
                            type="search"
                            x-model="q"
                            @input.debounce.350ms="fetch()"
                            placeholder="Tìm tên diễn viên..."
                            autocomplete="off"
                            autocorrect="off"
                            spellcheck="false"
                            class="pl-9 pr-8 py-2 text-sm border border-gray-200 rounded-xl bg-gray-50
                                   focus:outline-none focus:ring-2 focus:ring-sky-400 focus:border-transparent w-56 transition"
                        >
                        {{-- Clear button --}}
                        <button x-show="q.length > 0" @click="q = ''; fetch()"
                                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition"
                                title="Xóa">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Tabs (AJAX, no page reload) --}}
                <div class="flex gap-1 border-b border-gray-100 -mb-px overflow-x-auto" style="scrollbar-width:none">
                    @php
                        $tabs = [
                            ['label' => 'Tất cả',    'value' => ''],
                            ['label' => 'Diễn viên', 'value' => 'Acting'],
                            ['label' => 'Đạo diễn',  'value' => 'Directing'],
                            ['label' => 'Biên kịch',  'value' => 'Writing'],
                            ['label' => 'Sản xuất',  'value' => 'Production'],
                        ];
                    @endphp
                    @foreach($tabs as $tab)
                        <button
                            @click="setRole('{{ $tab['value'] }}')"
                            class="shrink-0 px-4 py-3 text-sm font-medium border-b-2 transition-colors"
                            :class="role === '{{ $tab['value'] }}'
                                ? 'border-sky-500 text-sky-600'
                                : 'border-transparent text-gray-500 hover:text-gray-900 hover:border-gray-300'"
                        >{{ $tab['label'] }}</button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── Grid ── --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            {{-- Loading skeleton --}}
            <div x-show="loading && items.length === 0"
                 class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 lg:gap-5">
                @for($i = 0; $i < 12; $i++)
                    <div class="flex flex-col items-center p-3 animate-pulse">
                        <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-full bg-gray-200"></div>
                        <div class="mt-3 w-16 h-3 bg-gray-200 rounded"></div>
                        <div class="mt-1.5 w-10 h-2.5 bg-gray-100 rounded"></div>
                    </div>
                @endfor
            </div>

            {{-- Empty state --}}
            <div x-show="!loading && items.length === 0" class="flex flex-col items-center justify-center py-24 text-center">
                <svg class="w-16 h-16 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <p class="text-gray-400 text-lg font-medium">Không tìm thấy ai</p>
                <button @click="q = ''; role = ''; fetch()" class="mt-4 text-sm text-sky-600 hover:underline">Xóa bộ lọc</button>
            </div>

            {{-- Results grid --}}
            <div x-show="items.length > 0"
                 class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 lg:gap-5">
                <template x-for="person in items" :key="person.url">
                    <a :href="person.url"
                       class="group flex flex-col items-center text-center rounded-2xl p-3 hover:bg-gray-50 transition-all duration-200">
                        {{-- Avatar --}}
                        <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-full overflow-hidden bg-gray-100 shadow-md
                                    ring-2 ring-transparent group-hover:ring-sky-400 transition-all duration-200 shrink-0">
                            <template x-if="person.photo">
                                <img :src="person.photo" :alt="person.name"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                     loading="lazy">
                            </template>
                            <template x-if="!person.photo">
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                            </template>
                        </div>

                        {{-- Name --}}
                        <div class="mt-3 w-full">
                            <p class="text-sm font-semibold text-gray-900 group-hover:text-sky-600 transition-colors line-clamp-2 leading-tight"
                               x-text="person.name"></p>
                            <p x-show="person.known_for"
                               class="text-xs text-gray-400 mt-0.5"
                               x-text="roleMap[person.known_for] ?? person.known_for"></p>
                            <p x-show="person.movies_count > 0"
                               class="text-[11px] text-gray-300 mt-0.5"
                               x-text="person.movies_count + ' phim'"></p>
                        </div>
                    </a>
                </template>
            </div>

            {{-- Load more --}}
            <div x-show="hasMore" class="mt-10 flex justify-center">
                <button @click="loadMore()"
                        :disabled="loading"
                        class="px-6 py-2.5 text-sm font-semibold text-sky-600 border border-sky-200 rounded-xl
                               hover:bg-sky-50 transition-colors disabled:opacity-50 flex items-center gap-2">
                    <svg x-show="loading"
                         class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span x-text="loading ? 'Đang tải...' : 'Tải thêm'"></span>
                </button>
            </div>

        </div>
    </div>
</x-app-layout>
