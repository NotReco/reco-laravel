{{-- Movie Card Component (TMDB-style dropdown) --}}
{{-- Usage: <x-movie-card :movie="$movie" /> --}}

@props(['movie', 'rank' => null, 'showGenre' => false, 'hideOriginalTitle' => false])

<div {{ $attributes->merge(['class' => 'group block relative']) }} x-data="{
    open: false,
    inWatchlist: {{ auth()->check() && auth()->user()->watchlists()->where('movies.id', $movie->id)->exists() ? 'true' : 'false' }},
    watchlistStatus: '{{ auth()->check() ? auth()->user()->watchlists()->where('movies.id', $movie->id)->value('watchlists.status') ?? '' : '' }}',
    isFavorited: {{ auth()->check() && auth()->user()->favorites()->where('movies.id', $movie->id)->exists() ? 'true' : 'false' }},

    toggle() {
        if (this.open) {
            this.open = false;
        } else {
            window.dispatchEvent(new CustomEvent('close-all-card-menus'));
            this.open = true;
        }
    },

    async toggleWatchlist(status) {
        @auth
try {
                const res = await fetch('{{ route('watchlist.toggle') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ movie_id: {{ $movie->id }}, status: status })
                });
                const data = await res.json();
                if (data.success) {
                    this.inWatchlist = data.in_watchlist;
                    this.watchlistStatus = data.status ?? '';
                }
            } catch(e) {}
            this.open = false; @endauth
    },

    async toggleFavorite() {
        @auth
try {
                const res = await fetch('{{ route('favorites.toggle') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ movie_id: {{ $movie->id }} })
                });
                const data = await res.json();
                if (data.success) { this.isFavorited = data.is_favorited; }
            } catch(e) {}
            this.open = false; @endauth
    }
}"
    @close-all-card-menus.window="open = false" @click.outside="open = false" @scroll.window="open = false"
    @contextmenu.window="open = false">
    {{-- Poster wrapper --}}
    <div class="relative aspect-[2/3] rounded-xl overflow-hidden bg-gray-100 shadow-sm">
        {{-- Link to movie --}}
        <a href="{{ route('movies.show', $movie) }}" class="absolute inset-0 z-0">
            @if ($movie->poster)
                <img src="{{ $movie->poster }}" alt="{{ $movie->title }}"
                    class="w-full h-full object-cover transition-all duration-300 bg-gray-200" loading="lazy">
            @else
                <div class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-100">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                    </svg>
                </div>
            @endif
        </a>

        {{-- Rating badge (bottom-left) --}}
        @if ($movie->avg_rating > 0)
            <div class="absolute bottom-2 left-2 z-10 badge-dark shadow-sm pointer-events-none" x-show="!open">
                <svg class="w-3.5 h-3.5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                <span>{{ number_format($movie->avg_rating, 1) }}</span>
            </div>
        @endif

        {{-- Dropdown overlay (centered inside poster, TMDB-style) --}}
        <div x-show="open" x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95" @click.stop
            class="absolute inset-0 z-20 flex items-center justify-center p-3 bg-gray-900/60 backdrop-blur-md"
            style="display:none;">

            <div class="w-full max-w-[200px] bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden">
                @auth
                    {{-- Watchlist options --}}
                    <div class="py-1.5">
                        <p class="px-3 pt-1 pb-1 text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Thêm vào
                            danh sách</p>

                        <button @click="toggleWatchlist('want_to_watch')"
                            class="w-full flex items-center gap-2.5 px-3 py-1.5 text-xs hover:bg-gray-50 transition-colors text-left"
                            :class="watchlistStatus === 'want_to_watch' ? 'text-sky-600 bg-sky-50' : 'text-gray-700'">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                            </svg>
                            <span>Muốn xem</span>
                            <svg x-show="watchlistStatus === 'want_to_watch'" class="w-3 h-3 ml-auto text-sky-500 shrink-0"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>

                        <button @click="toggleWatchlist('watching')"
                            class="w-full flex items-center gap-2.5 px-3 py-1.5 text-xs hover:bg-gray-50 transition-colors text-left"
                            :class="watchlistStatus === 'watching' ? 'text-blue-600 bg-blue-50' : 'text-gray-700'">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Đang xem</span>
                            <svg x-show="watchlistStatus === 'watching'" class="w-3 h-3 ml-auto text-blue-500 shrink-0"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>

                        <button @click="toggleWatchlist('watched')"
                            class="w-full flex items-center gap-2.5 px-3 py-1.5 text-xs hover:bg-gray-50 transition-colors text-left"
                            :class="watchlistStatus === 'watched' ? 'text-green-600 bg-green-50' : 'text-gray-700'">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Đã xem</span>
                            <svg x-show="watchlistStatus === 'watched'" class="w-3 h-3 ml-auto text-green-500 shrink-0"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>

                        <button @click="toggleWatchlist('dropped')"
                            class="w-full flex items-center gap-2.5 px-3 py-1.5 text-xs hover:bg-gray-50 transition-colors text-left"
                            :class="watchlistStatus === 'dropped' ? 'text-gray-500 bg-gray-100' : 'text-gray-700'">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span>Bỏ dở</span>
                            <svg x-show="watchlistStatus === 'dropped'" class="w-3 h-3 ml-auto text-gray-400 shrink-0"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>

                    <div class="border-t border-gray-100 py-1.5">
                        <button @click="toggleFavorite()"
                            class="w-full flex items-center gap-2.5 px-3 py-1.5 text-xs hover:bg-gray-50 transition-colors text-left"
                            :class="isFavorited ? 'text-rose-500' : 'text-gray-700'">
                            <svg class="w-3.5 h-3.5 shrink-0" :fill="isFavorited ? 'currentColor' : 'none'"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            <span x-text="isFavorited ? 'Bỏ yêu thích' : 'Yêu thích'"></span>
                        </button>

                        <a href="{{ route('movies.show', $movie) }}#review-form"
                            class="w-full flex items-center gap-2.5 px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-50 transition-colors">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                            <span>Đánh giá</span>
                        </a>
                    </div>
                @else
                    {{-- Not logged in --}}
                    <div class="py-3 px-4 text-left">
                        <p class="text-xs text-gray-600 leading-snug">Muốn đánh giá hoặc thêm vào danh sách?</p>
                        <a href="{{ route('login') }}"
                            class="mt-1.5 inline-block text-xs font-semibold text-sky-600 hover:text-sky-700 hover:underline">Đăng
                            nhập</a>
                    </div>
                    <div class="border-t-2 border-gray-200"></div>
                    <div class="py-3 px-4 text-left">
                        <p class="text-xs text-gray-600 leading-snug">Chưa là thành viên?</p>
                        <a href="{{ route('register') }}"
                            class="mt-1.5 inline-block text-xs font-semibold text-sky-600 hover:text-sky-700 hover:underline">Đăng
                            ký</a>
                    </div>
                @endauth
            </div>
        </div>
    </div>

    {{-- 3-dot button (top-right, OUTSIDE poster overflow, hidden when dropdown open) --}}
    <div class="absolute top-2 right-2 z-30" x-show="!open">
        <button @click.stop.prevent="toggle()"
            class="w-7 h-7 rounded-full bg-gray-500/70 backdrop-blur-sm text-white flex items-center justify-center hover:bg-sky-500/80 transition-colors shadow">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path
                    d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
            </svg>
        </button>
    </div>

    {{-- Title & Meta --}}
    <a href="{{ route('movies.show', $movie) }}" class="block mt-2">
        <h3 class="text-sm font-bold text-gray-900 group-hover:text-sky-600 transition-colors font-heading">
            {{ $movie->title }}
            @if (!isset($hideOriginalTitle) || !$hideOriginalTitle)
                @if ($movie->original_title && $movie->original_title !== $movie->title)
                    <span class="text-xs text-gray-500 font-normal">({{ $movie->original_title }})</span>
                @endif
            @endif
        </h3>
        <div class="flex items-center gap-2 text-xs text-gray-500 font-medium mt-0.5">
            @if ($movie->release_date)
                <span>{{ $movie->release_date->format('d/m/Y') }}</span>
            @endif
        </div>
    </a>
</div>
