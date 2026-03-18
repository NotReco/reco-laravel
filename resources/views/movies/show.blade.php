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

        {{-- Backdrop Hero --}}
        <section data-hero-dark class="relative h-[400px] md:h-[500px] overflow-hidden -mt-16">
            @if($movie->backdrop)
                <img src="{{ $movie->backdrop }}" alt="{{ $movie->title }}" class="w-full h-full object-cover">
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-dark-950 via-dark-950/70 to-dark-950/30"></div>
        </section>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-48 relative z-10 pb-12">

            {{-- Movie Info --}}
            <div class="flex flex-col md:flex-row gap-8 mb-12">
                {{-- Poster --}}
                <div class="shrink-0 w-48 mx-auto md:mx-0 md:w-64">
                    @if($movie->poster)
                        <img src="{{ $movie->poster }}" alt="{{ $movie->title }}"
                            class="w-full rounded-2xl shadow-2xl shadow-dark-950/50 border border-dark-700/50">
                    @else
                        <div class="w-full aspect-[2/3] rounded-2xl bg-dark-800 flex items-center justify-center border border-dark-700/50 shadow-2xl">
                            <svg class="w-16 h-16 text-dark-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/></svg>
                        </div>
                    @endif
                </div>

                {{-- Details --}}
                <div class="flex-1 space-y-5 pt-4 md:pt-16 text-center md:text-left">
                    <div>
                        <h1 class="text-3xl lg:text-5xl font-display font-bold text-white">{{ $movie->title }}</h1>
                        @if($movie->original_title && $movie->original_title !== $movie->title)
                            <p class="text-dark-400 text-sm italic mt-1">{{ $movie->original_title }}</p>
                        @endif
                    </div>

                    @if($movie->tagline)
                        <p class="text-rose-400 font-medium italic text-lg shadow-sm">"{{ $movie->tagline }}"</p>
                    @endif

                    {{-- Meta --}}
                    <div class="flex items-center justify-center md:justify-start gap-4 flex-wrap text-sm text-dark-300">
                        @if($movie->release_date)
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="4"/></svg>
                                {{ \Carbon\Carbon::parse($movie->release_date)->format('d/m/Y') }}
                            </span>
                        @endif
                        @if($movie->runtime)
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                                {{ $movie->runtime }} phút
                            </span>
                        @endif
                        @if($movie->country)
                            <span>🌍 {{ $movie->country }}</span>
                        @endif
                        @if($movie->tmdb_id)
                            <a href="https://www.themoviedb.org/movie/{{ $movie->tmdb_id }}" target="_blank" class="flex items-center gap-1 text-dark-400 hover:text-rose-400 transition-colors" title="Xem trên The Movie Database">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22.766 5.868c-.015-.008-1.517-.775-4.634-2.14a26.55 26.55 0 00-6.132-.988 26.55 26.55 0 00-6.132.988c-3.117 1.365-4.619 2.132-4.634 2.14-1.127.697-1.127 1.95 0 2.646.685.424 1.41.801 2.164 1.13 2.651 1.154 5.562 1.745 8.602 1.745s5.951-.59 8.602-1.745c.754-.329 1.479-.706 2.164-1.13 1.127-.696 1.127-1.949 0-2.646zM12 11.22c-3.04 0-5.951-.59-8.602-1.745a22.254 22.254 0 01-2.164-1.13l-.001 8.526c0 1.252 1.583 1.742 2.646 2.399 2.508 1.551 5.347 2.37 8.121 2.37s5.613-.819 8.121-2.37c1.063-.657 2.646-1.147 2.646-2.399V8.345a22.254 22.254 0 01-2.164 1.13C17.951 10.63 15.04 11.22 12 11.22zm-3.464 7.012c-.828 0-1.5-.672-1.5-1.5s.672-1.5 1.5-1.5 1.5.672 1.5 1.5-.672 1.5-1.5 1.5zm6.928 0c-.828 0-1.5-.672-1.5-1.5s.672-1.5 1.5-1.5 1.5.672 1.5 1.5-.672 1.5-1.5 1.5z"/></svg>
                                TMDb ID: <span class="font-mono bg-dark-800 px-1.5 py-0.5 rounded text-white text-xs border border-dark-700">{{ $movie->tmdb_id }}</span>
                            </a>
                        @endif
                    </div>

                    {{-- Rating --}}
                    @if($avgRating > 0)
                        <div class="flex items-center justify-center md:justify-start gap-4">
                            <div class="glass-light rounded-xl px-4 py-2 flex items-center gap-4">
                                <span class="text-3xl font-bold text-white">{{ number_format($avgRating, 1) }}</span>
                                <div>
                                    <x-star-rating :rating="$avgRating" :max="10" size="md" />
                                    <p class="text-xs text-dark-400 mt-0.5">{{ count($movie->reviews) }} đánh giá cộng đồng</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Genres --}}
                    @if($movie->genres->isNotEmpty())
                        <div class="flex items-center justify-center md:justify-start gap-2 flex-wrap">
                            @foreach($movie->genres as $genre)
                                <a href="{{ route('explore', ['genre' => $genre->id]) }}" class="badge-dark hover:text-rose-400 transition-colors">
                                    {{ $genre->name }}
                                </a>
                            @endforeach
                        </div>
                    @endif

                    {{-- Synopsis --}}
                    @if($movie->synopsis)
                        <div class="text-left">
                            <h3 class="font-display font-semibold text-white mb-2 text-lg">Nội dung</h3>
                            <x-expandable-text :text="$movie->synopsis" :maxLength="300" />
                        </div>
                    @endif

                    {{-- Actions & Interactions --}}
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 mt-6 pt-6 border-t border-dark-700/50">
                        @if($movie->trailer_url)
                            <button @click="openTrailer('{{ $movie->trailer_url }}')" class="btn-rose flex-1 md:flex-none justify-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/></svg>
                                Trailer
                            </button>
                        @endif
                        <a href="#review-form" class="btn-ghost flex-1 md:flex-none justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            Review
                        </a>

                        {{-- User Actions --}}
                        <div class="flex items-center gap-2 w-full md:w-auto mt-2 md:mt-0" x-data="{
                            isFavorited: {{ auth()->check() && $movie->favoritedBy->contains(auth()->id()) ? 'true' : 'false' }},
                            watchlistStatus: '{{ auth()->check() ? optional($movie->watchlistedBy->where('id', auth()->id())->first())->pivot->status : '' }}',
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
                                if(data.success) {
                                    this.watchlistStatus = data.in_watchlist ? data.status : '';
                                    // Optional: show a toast here using Alpine events
                                }
                            }
                        }">
                            {{-- Favorite --}}
                            <button @click="toggleFavorite()" 
                                class="flex-1 md:flex-none p-2.5 rounded-xl border transition-all flex items-center justify-center group"
                                :class="isFavorited ? 'bg-rose-900/30 border-rose-500/50 text-rose-400' : 'bg-dark-800 border-dark-600 text-dark-300 hover:text-white hover:border-dark-500'">
                                <svg class="w-5 h-5 transition-transform group-active:scale-75" :fill="isFavorited ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            </button>

                            {{-- Watchlist Dropdown --}}
                            <div class="relative flex-1 md:flex-none" x-data="{ open: false }" @click.outside="open = false">
                                <button @click="open = !open" 
                                    class="w-full p-2.5 rounded-xl border transition-all flex items-center justify-center gap-2 group"
                                    :class="watchlistStatus ? 'bg-blue-900/30 border-blue-500/50 text-blue-400' : 'bg-dark-800 border-dark-600 text-dark-300 hover:text-white hover:border-dark-500'">
                                    <svg class="w-5 h-5 transition-transform group-active:scale-75" :fill="watchlistStatus ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                                    </svg>
                                    <span class="text-sm font-medium" x-text="watchlistStatus === 'want_to_watch' ? 'Muốn xem' : (watchlistStatus === 'watching' ? 'Đang xem' : (watchlistStatus === 'watched' ? 'Đã xem' : (watchlistStatus === 'dropped' ? 'Bỏ dở' : 'Watchlist')))"></span>
                                </button>

                                <div x-show="open" x-transition.opacity.duration.200ms class="absolute right-0 mt-2 w-48 bg-dark-800 border border-dark-600 rounded-xl shadow-xl overflow-hidden z-20" style="display: none;">
                                    <button @click="updateWatchlist('want_to_watch'); open = false" class="w-full text-left px-4 py-2 text-sm text-dark-300 hover:text-white hover:bg-dark-700 transition" :class="{'text-blue-400 bg-dark-700/50': watchlistStatus === 'want_to_watch'}">Muốn xem</button>
                                    <button @click="updateWatchlist('watching'); open = false" class="w-full text-left px-4 py-2 text-sm text-dark-300 hover:text-white hover:bg-dark-700 transition" :class="{'text-blue-400 bg-dark-700/50': watchlistStatus === 'watching'}">Đang xem</button>
                                    <button @click="updateWatchlist('watched'); open = false" class="w-full text-left px-4 py-2 text-sm text-dark-300 hover:text-white hover:bg-dark-700 transition" :class="{'text-blue-400 bg-dark-700/50': watchlistStatus === 'watched'}">Đã xem</button>
                                    <button @click="updateWatchlist('dropped'); open = false" class="w-full text-left px-4 py-2 text-sm text-dark-300 hover:text-white hover:bg-dark-700 transition" :class="{'text-blue-400 bg-dark-700/50': watchlistStatus === 'dropped'}">Bỏ dở</button>
                                    <button x-show="watchlistStatus" @click="updateWatchlist(watchlistStatus); open = false" class="w-full text-left px-4 py-2 text-sm text-rose-400 hover:bg-dark-700 transition border-t border-dark-600">Gỡ khỏi danh sách</button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Directors & Writers --}}
            @if($directors->isNotEmpty() || $writers->isNotEmpty())
                <div class="card p-6 mb-12">
                    <div class="grid sm:grid-cols-2 gap-6">
                        @if($directors->isNotEmpty())
                            <div>
                                <h3 class="font-display font-semibold text-white text-sm mb-2 text-rose-400">🎬 ĐẠO DIỄN</h3>
                                <p class="text-white text-sm">{{ $directors->pluck('name')->join(', ') }}</p>
                            </div>
                        @endif
                        @if($writers->isNotEmpty())
                            <div>
                                <h3 class="font-display font-semibold text-white text-sm mb-2 text-rose-400">✍️ BIÊN KỊCH</h3>
                                <p class="text-white text-sm">{{ $writers->pluck('name')->join(', ') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Cast --}}
            @if($cast->isNotEmpty())
                <section class="mb-12">
                    <h2 class="section-title mb-6">🎭 Diễn viên</h2>
                    <div class="flex gap-4 overflow-x-auto pb-4 scrollbar-hide snap-x">
                        @foreach($cast as $person)
                            <a href="{{ route('person.show', $person) }}" class="shrink-0 w-32 text-center snap-start group block">
                            <div class="w-32 h-32 rounded-full overflow-hidden mb-3 border-2 border-transparent group-hover:border-rose-500 transition-all duration-300">
                                @if($person->photo)
                                    <img src="{{ $person->photo }}" alt="{{ $person->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" loading="lazy">
                                @else
                                    <div class="w-full h-full bg-dark-800 flex items-center justify-center text-dark-500 group-hover:bg-dark-700 transition-colors">
                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    </div>
                                @endif
                            </div>
                            <p class="text-white text-sm font-semibold line-clamp-1 group-hover:text-rose-400 transition-colors">{{ $person->name }}</p>
                            @if($person->pivot->character_name)
                                <p class="text-dark-400 text-xs line-clamp-1 mt-0.5">{{ $person->pivot->character_name }}</p>
                            @endif
                        </a>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- Review Form Section --}}
            <section class="mb-12" id="review-form">
                <x-review-form :movieId="$movie->id" />
            </section>

            {{-- Reviews List --}}
            @if($movie->reviews->isNotEmpty())
                <section class="mb-12">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="section-title">💬 Đánh giá cộng đồng</h2>
                        <span class="badge-dark">{{ count($movie->reviews) }} reviews</span>
                    </div>

                    <div class="grid md:grid-cols-2 gap-5">
                        @foreach($movie->reviews as $review)
                            <x-review-card :review="$review" :showMovie="false" />
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- Related Movies --}}
            @if($relatedMovies->isNotEmpty())
                <x-movie-section
                    title="🎬 Phim liên quan"
                    :items="$relatedMovies"
                    layout="grid"
                    class="mt-16"
                />
            @endif

        </div>

        {{-- Trailer Modal --}}
        <x-trailer-modal />
    </div>
</x-app-layout>