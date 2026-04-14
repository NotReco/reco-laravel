<x-app-layout>
    <x-slot:title>{{ $movie->title }}</x-slot:title>

    <div x-data="{
        showTrailer: false,
        trailerUrl: '',
        openTrailer(url) {
            if (!url) return;
            const videoId = url.includes('v=') ? url.split('v=')[1].split('&')[0] : url.split('/').pop();
            this.trailerUrl = 'https://www.youtube.com/embed/' + videoId + '?autoplay=1&rel=0';
            this.showTrailer = true;
        },
        closeTrailer() {
            this.showTrailer = false;
            this.trailerUrl = '';
        }
    }" @keydown.escape.window="closeTrailer()">

        {{-- ══════════════════════════════════════════════════ --}}
        {{-- HERO: Backdrop + Gradient to white                 --}}
        {{-- ══════════════════════════════════════════════════ --}}
        <div class="relative -mt-16 overflow-hidden" style="height: 520px;">
            {{-- Backdrop Image --}}
            @if($movie->backdrop)
                <img src="{{ $movie->backdrop }}" alt="{{ $movie->title }}"
                    class="absolute inset-0 w-full h-full object-cover object-center">
            @else
                <div class="absolute inset-0 bg-gradient-to-br from-gray-800 via-gray-900 to-gray-950"></div>
            @endif

            {{-- Dark overlay for contrast --}}
            <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/50 to-black/30"></div>

            {{-- Fade to white at the bottom --}}
            <div class="absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-white to-transparent"></div>
        </div>

        {{-- ══════════════════════════════════════════════════ --}}
        {{-- CONTENT AREA: Light themed                        --}}
        {{-- ══════════════════════════════════════════════════ --}}
        <div class="bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                {{-- ── MOVIE INFO BLOCK ── --}}
                <div class="flex flex-col md:flex-row gap-8 -mt-52 mb-10">

                    {{-- Poster --}}
                    <div class="shrink-0 w-40 md:w-56 mx-auto md:mx-0 relative z-10">
                        @if($movie->poster)
                            <div class="rounded-2xl overflow-hidden shadow-2xl ring-4 ring-white">
                                <img src="{{ $movie->poster }}" alt="{{ $movie->title }}"
                                    class="w-full block">
                            </div>
                        @else
                            <div class="w-full aspect-[2/3] rounded-2xl bg-gray-200 flex items-center justify-center shadow-2xl ring-4 ring-white">
                                <svg class="w-14 h-14 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/></svg>
                            </div>
                        @endif
                    </div>

                    {{-- Details --}}
                    <div class="flex-1 pt-4 md:pt-6 relative z-10">

                        {{-- Title (white while still over hero gradient) --}}
                        <div class="mb-3">
                            <h1 class="text-3xl lg:text-4xl font-display font-bold text-white drop-shadow-lg">{{ $movie->title }}</h1>
                            @if($movie->original_title && $movie->original_title !== $movie->title)
                                <p class="text-gray-300 text-sm italic mt-1 drop-shadow">{{ $movie->original_title }}</p>
                            @endif
                        </div>

                        @if($movie->tagline)
                            <p class="text-sky-300 font-medium italic text-base mb-4 drop-shadow">"{{ $movie->tagline }}"</p>
                        @endif

                        {{-- Meta Info --}}
                        <div class="flex items-center flex-wrap gap-3 text-sm mb-4">
                            @if($movie->release_date)
                                <span class="flex items-center gap-1.5 bg-black/30 backdrop-blur-sm text-white px-3 py-1.5 rounded-full border border-white/20">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    {{ \Carbon\Carbon::parse($movie->release_date)->format('d/m/Y') }}
                                </span>
                            @endif
                            @if($movie->runtime)
                                <span class="flex items-center gap-1.5 bg-black/30 backdrop-blur-sm text-white px-3 py-1.5 rounded-full border border-white/20">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $movie->runtime }} phút
                                </span>
                            @endif
                            @if($movie->country)
                                <span class="flex items-center gap-1.5 bg-black/30 backdrop-blur-sm text-white px-3 py-1.5 rounded-full border border-white/20">
                                    🌍 {{ $movie->country }}
                                </span>
                            @endif
                        </div>

                        {{-- Genres --}}
                        @if($movie->genres->isNotEmpty())
                            <div class="flex flex-wrap gap-2 mb-6">
                                @foreach($movie->genres as $genre)
                                    <a href="{{ route('explore', ['genre' => $genre->id]) }}"
                                        class="px-3 py-1 text-sm font-medium bg-sky-50 text-sky-600 border border-sky-200 rounded-full hover:bg-sky-100 transition-colors">
                                        {{ $genre->name }}
                                    </a>
                                @endforeach
                            </div>
                        @endif

                        {{-- Rating + Actions row --}}
                        <div class="flex flex-wrap items-center gap-4">

                            {{-- Score badge --}}
                            @if($avgRating > 0)
                                @php
                                    $scoreColor = match(true) {
                                        $avgRating >= 9 => ['ring' => 'ring-yellow-400', 'text' => 'text-yellow-600', 'bg' => 'bg-yellow-50'],
                                        $avgRating >= 7 => ['ring' => 'ring-teal-400', 'text' => 'text-teal-600', 'bg' => 'bg-teal-50'],
                                        $avgRating >= 5 => ['ring' => 'ring-orange-400', 'text' => 'text-orange-600', 'bg' => 'bg-orange-50'],
                                        default         => ['ring' => 'ring-red-400', 'text' => 'text-red-600', 'bg' => 'bg-red-50'],
                                    };
                                @endphp
                                <div class="flex items-center gap-3 bg-white rounded-2xl shadow-md border border-gray-100 px-4 py-2.5">
                                    <div class="w-12 h-12 rounded-full {{ $scoreColor['bg'] }} {{ $scoreColor['ring'] }} ring-2 flex items-center justify-center shrink-0">
                                        <span class="text-lg font-black {{ $scoreColor['text'] }}">{{ number_format($avgRating, 1) }}</span>
                                    </div>
                                    <div>
                                        <x-star-rating :rating="$avgRating" :max="10" size="sm" />
                                        <p class="text-xs text-gray-400 mt-0.5">{{ count($movie->reviews) }} đánh giá</p>
                                    </div>
                                </div>
                            @endif

                            {{-- Actions --}}
                            @if($movie->trailer_url)
                                <button @click="openTrailer('{{ $movie->trailer_url }}')"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg shadow-sky-600/30">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/></svg>
                                    Trailer
                                </button>
                            @endif

                            <a href="#review-form"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-xl border border-gray-200 transition-all duration-200 shadow-sm">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                Viết review
                            </a>

                            {{-- Watchlist & Favorite --}}
                            <div class="flex items-center gap-2" x-data="{
                                isFavorited: {{ auth()->check() && $movie->favoritedBy->contains(auth()->id()) ? 'true' : 'false' }},
                                watchlistStatus: '{{ auth()->check() ? ($movie->watchlistedBy->where('id', auth()->id())->first()?->pivot?->status ?? '') : '' }}',
                                async toggleFavorite() {
                                    @guest window.location.href = '{{ route('login') }}'; return; @endguest
                                    const res = await fetch('{{ route('favorites.toggle') }}', {
                                        method: 'POST',
                                        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                                        body: JSON.stringify({ movie_id: {{ $movie->id }} })
                                    });
                                    const data = await res.json();
                                    if(data.success) this.isFavorited = data.is_favorited;
                                },
                                async updateWatchlist(status) {
                                    @guest window.location.href = '{{ route('login') }}'; return; @endguest
                                    const res = await fetch('{{ route('watchlist.toggle') }}', {
                                        method: 'POST',
                                        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                                        body: JSON.stringify({ movie_id: {{ $movie->id }}, status: status })
                                    });
                                    const data = await res.json();
                                    if(data.success) this.watchlistStatus = data.in_watchlist ? data.status : '';
                                }
                            }">
                                {{-- Favorite --}}
                                <button @click="toggleFavorite()" title="Yêu thích"
                                    class="p-2.5 rounded-xl border transition-all"
                                    :class="isFavorited ? 'bg-sky-50 border-sky-300 text-sky-500' : 'bg-white border-gray-200 text-gray-400 hover:text-sky-500 hover:border-sky-300'">
                                    <svg class="w-5 h-5" :fill="isFavorited ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                </button>

                                {{-- Watchlist Dropdown --}}
                                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                                    <button @click="open = !open"
                                        class="flex items-center gap-2 px-4 py-2.5 rounded-xl border text-sm font-medium transition-all"
                                        :class="watchlistStatus ? 'bg-blue-50 border-blue-300 text-blue-600' : 'bg-white border-gray-200 text-gray-600 hover:border-gray-300'">
                                        <svg class="w-4 h-4" :fill="watchlistStatus ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                        </svg>
                                        <span x-text="watchlistStatus === 'want_to_watch' ? 'Muốn xem' : (watchlistStatus === 'watching' ? 'Đang xem' : (watchlistStatus === 'watched' ? 'Đã xem' : (watchlistStatus === 'dropped' ? 'Bỏ dở' : 'Watchlist')))"></span>
                                        <svg class="w-3.5 h-3.5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                    <div x-show="open" x-transition.opacity.duration.150ms
                                        class="absolute left-0 mt-2 w-44 bg-white border border-gray-100 rounded-xl shadow-xl overflow-hidden z-20"
                                        style="display: none;">
                                        <button @click="updateWatchlist('want_to_watch'); open = false" class="w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition" :class="{'text-blue-600 bg-blue-50': watchlistStatus === 'want_to_watch'}">Muốn xem</button>
                                        <button @click="updateWatchlist('watching'); open = false" class="w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition" :class="{'text-blue-600 bg-blue-50': watchlistStatus === 'watching'}">Đang xem</button>
                                        <button @click="updateWatchlist('watched'); open = false" class="w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition" :class="{'text-blue-600 bg-blue-50': watchlistStatus === 'watched'}">Đã xem</button>
                                        <button @click="updateWatchlist('dropped'); open = false" class="w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition" :class="{'text-blue-600 bg-blue-50': watchlistStatus === 'dropped'}">Bỏ dở</button>
                                        <button x-show="watchlistStatus" @click="updateWatchlist(watchlistStatus); open = false" class="w-full text-left px-4 py-2.5 text-sm text-sky-500 hover:bg-sky-50 transition border-t border-gray-100">Gỡ khỏi danh sách</button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- ══════════════════════════════════════════════════ --}}
                {{-- MAIN CONTENT GRID                                 --}}
                {{-- ══════════════════════════════════════════════════ --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pb-16">

                    {{-- Left Column (main content) --}}
                    <div class="lg:col-span-2 space-y-8">

                        {{-- Synopsis --}}
                        @if($movie->synopsis)
                            <section>
                                <h2 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2">
                                    <span class="w-1 h-5 bg-sky-500 rounded-full inline-block"></span>
                                    Nội dung
                                </h2>
                                <div class="text-gray-600 leading-relaxed text-sm">
                                    <x-expandable-text :text="$movie->synopsis" :maxLength="350" />
                                </div>
                            </section>
                        @endif

                        {{-- Directors & Writers --}}
                        @if($directors->isNotEmpty() || $writers->isNotEmpty())
                            <section>
                                <h2 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2">
                                    <span class="w-1 h-5 bg-sky-500 rounded-full inline-block"></span>
                                    Đội ngũ sản xuất
                                </h2>
                                <div class="grid sm:grid-cols-2 gap-4">
                                    @if($directors->isNotEmpty())
                                        <div class="bg-gray-50 border border-gray-100 rounded-2xl p-4">
                                            <p class="text-xs font-semibold text-sky-500 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                Đạo diễn
                                            </p>
                                            <p class="text-gray-800 font-medium text-sm">{{ $directors->pluck('name')->join(', ') }}</p>
                                        </div>
                                    @endif
                                    @if($writers->isNotEmpty())
                                        <div class="bg-gray-50 border border-gray-100 rounded-2xl p-4">
                                            <p class="text-xs font-semibold text-sky-500 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                Biên kịch
                                            </p>
                                            <p class="text-gray-800 font-medium text-sm">{{ $writers->pluck('name')->join(', ') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </section>
                        @endif

                        {{-- Cast --}}
                        @if($cast->isNotEmpty())
                            <section>
                                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                    <span class="w-1 h-5 bg-sky-500 rounded-full inline-block"></span>
                                    Diễn viên
                                </h2>
                                <div class="flex gap-4 overflow-x-auto pb-3 scrollbar-hide snap-x -mx-1 px-1">
                                    @foreach($cast as $person)
                                        <a href="{{ route('person.show', $person) }}"
                                            class="shrink-0 w-24 text-center snap-start group">
                                            <div class="w-20 h-20 mx-auto rounded-full overflow-hidden mb-2 ring-2 ring-transparent group-hover:ring-sky-400 transition-all duration-200 shadow-md">
                                                @if($person->photo)
                                                    <img src="{{ $person->photo }}" alt="{{ $person->name }}"
                                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300" loading="lazy">
                                                @else
                                                    <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <p class="text-xs font-semibold text-gray-800 line-clamp-1 group-hover:text-sky-500 transition-colors">{{ $person->name }}</p>
                                            @if($person->pivot->character_name)
                                                <p class="text-[10px] text-gray-400 line-clamp-1 mt-0.5">{{ $person->pivot->character_name }}</p>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </section>
                        @endif

                        {{-- Reviews List --}}
                        @if($movie->reviews->isNotEmpty())
                            <section>
                                <div class="flex items-center justify-between mb-4">
                                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                        <span class="w-1 h-5 bg-sky-500 rounded-full inline-block"></span>
                                        Đánh giá cộng đồng
                                    </h2>
                                    <span class="text-xs font-semibold text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">{{ count($movie->reviews) }} reviews</span>
                                </div>
                                <div class="grid md:grid-cols-2 gap-4">
                                    @foreach($movie->reviews as $review)
                                        <x-review-card :review="$review" :showMovie="false" />
                                    @endforeach
                                </div>
                            </section>
                        @endif

                        {{-- Review Form --}}
                        <section id="review-form">
                            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <span class="w-1 h-5 bg-sky-500 rounded-full inline-block"></span>
                                Viết đánh giá
                            </h2>
                            <x-review-form :movie="$movie" />
                        </section>

                    </div>

                    {{-- Right Column (sidebar info) --}}
                    <div class="space-y-6">

                        {{-- Movie Facts Card --}}
                        <div class="bg-gray-50 border border-gray-100 rounded-2xl p-5">
                            <h3 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-wider">Thông tin</h3>
                            <dl class="space-y-3">
                                @if($movie->release_date)
                                    <div class="flex justify-between text-sm">
                                        <dt class="text-gray-500 font-medium">Khởi chiếu</dt>
                                        <dd class="text-gray-800 font-semibold">{{ \Carbon\Carbon::parse($movie->release_date)->format('d/m/Y') }}</dd>
                                    </div>
                                @endif
                                @if($movie->runtime)
                                    <div class="flex justify-between text-sm">
                                        <dt class="text-gray-500 font-medium">Thời lượng</dt>
                                        <dd class="text-gray-800 font-semibold">{{ $movie->runtime }} phút</dd>
                                    </div>
                                @endif
                                @if($movie->country)
                                    <div class="flex justify-between text-sm">
                                        <dt class="text-gray-500 font-medium">Quốc gia</dt>
                                        <dd class="text-gray-800 font-semibold">{{ $movie->country }}</dd>
                                    </div>
                                @endif
                                @if($movie->genres->isNotEmpty())
                                    <div class="flex justify-between items-start text-sm">
                                        <dt class="text-gray-500 font-medium shrink-0 pt-0.5">Thể loại</dt>
                                        <dd class="text-right">
                                            <div class="flex flex-wrap gap-1 justify-end">
                                                @foreach($movie->genres as $genre)
                                                    <a href="{{ route('explore', ['genre' => $genre->id]) }}"
                                                        class="text-xs text-sky-600 hover:text-sky-700 font-medium">{{ $genre->name }}</a>
                                                    @if(!$loop->last)<span class="text-gray-300 text-xs">·</span>@endif
                                                @endforeach
                                            </div>
                                        </dd>
                                    </div>
                                @endif
                                @if($movie->tmdb_id)
                                    <div class="flex justify-between items-center text-sm pt-2 border-t border-gray-100">
                                        <dt class="text-gray-500 font-medium">TMDb</dt>
                                        <dd>
                                            <a href="https://www.themoviedb.org/movie/{{ $movie->tmdb_id }}" target="_blank"
                                                class="text-xs text-blue-500 hover:text-blue-600 font-mono font-semibold hover:underline transition-colors">
                                                #{{ $movie->tmdb_id }} ↗
                                            </a>
                                        </dd>
                                    </div>
                                @endif
                            </dl>
                        </div>

                        {{-- Score Summary (if has reviews) --}}
                        @if($avgRating > 0)
                            @php
                                $scoreBg = match(true) {
                                    $avgRating >= 9 => 'from-yellow-400 to-amber-500',
                                    $avgRating >= 7 => 'from-teal-400 to-emerald-500',
                                    $avgRating >= 5 => 'from-orange-400 to-sky-400',
                                    default         => 'from-red-400 to-red-600',
                                };
                            @endphp
                            <div class="rounded-2xl overflow-hidden border border-gray-100 shadow-sm">
                                <div class="bg-gradient-to-br {{ $scoreBg }} px-5 py-4 text-white text-center">
                                    <p class="text-5xl font-black">{{ number_format($avgRating, 1) }}</p>
                                    <p class="text-white/80 text-xs mt-1 font-medium">/ 10 · {{ count($movie->reviews) }} đánh giá</p>
                                </div>
                                <div class="bg-white px-4 py-3 text-center">
                                    <x-star-rating :rating="$avgRating" :max="10" size="md" />
                                </div>
                            </div>
                        @endif

                    </div>
                </div>

                {{-- Related Movies --}}
                @if($relatedMovies->isNotEmpty())
                    <div class="border-t border-gray-100 pt-10 pb-10">
                        <x-movie-section
                            title="Phim liên quan"
                            :items="$relatedMovies"
                            layout="grid"
                        />
                    </div>
                @endif

            </div>
        </div>

        {{-- Trailer Modal --}}
        <x-trailer-modal />
    </div>
</x-app-layout>