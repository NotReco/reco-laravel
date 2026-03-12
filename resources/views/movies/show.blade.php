<x-app-layout>
    <x-slot:title>{{ $movie->title }}</x-slot:title>

    {{-- Backdrop Hero --}}
    <section class="relative h-[400px] md:h-[500px] overflow-hidden -mt-16">
        @if($movie->backdrop)
            <img src="{{ $movie->backdrop }}" alt="{{ $movie->title }}" class="w-full h-full object-cover">
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-dark-950 via-dark-950/70 to-dark-950/30"></div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-48 relative z-10 pb-12">

        {{-- Movie Info --}}
        <div class="flex flex-col md:flex-row gap-8 mb-12">
            {{-- Poster --}}
            <div class="shrink-0 w-48 md:w-64">
                @if($movie->poster)
                    <img src="{{ $movie->poster }}" alt="{{ $movie->title }}"
                        class="w-full rounded-2xl shadow-2xl shadow-dark-950/50 border border-dark-700/50">
                @endif
            </div>

            {{-- Details --}}
            <div class="flex-1 space-y-4 pt-4 md:pt-16">
                <h1 class="text-3xl md:text-4xl font-display font-bold text-white">{{ $movie->title }}</h1>

                @if($movie->original_title && $movie->original_title !== $movie->title)
                    <p class="text-dark-400 text-sm italic">{{ $movie->original_title }}</p>
                @endif

                @if($movie->tagline)
                    <p class="text-accent-400 font-medium italic">"{{ $movie->tagline }}"</p>
                @endif

                {{-- Meta --}}
                <div class="flex items-center gap-4 flex-wrap text-sm text-dark-300">
                    @if($movie->release_date)
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ \Carbon\Carbon::parse($movie->release_date)->format('d/m/Y') }}
                        </span>
                    @endif
                    @if($movie->runtime)
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $movie->runtime }} phút
                        </span>
                    @endif
                    @if($movie->country)
                        <span>🌍 {{ $movie->country }}</span>
                    @endif
                </div>

                {{-- Rating --}}
                @if($avgRating)
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <div class="w-14 h-14 rounded-xl bg-accent-500 flex items-center justify-center">
                                <span class="text-white text-xl font-bold">{{ number_format($avgRating, 1) }}</span>
                            </div>
                            <div>
                                <div class="flex items-center gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= round($avgRating / 2) ? 'text-accent-400' : 'text-dark-600' }}"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @endfor
                                </div>
                                <p class="text-dark-400 text-xs mt-0.5">{{ $ratingCount }} đánh giá</p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Genres --}}
                @if($movie->genres->isNotEmpty())
                    <div class="flex items-center gap-2 flex-wrap">
                        @foreach($movie->genres as $genre)
                            <a href="{{ route('explore', ['genre' => $genre->id]) }}"
                                class="px-3 py-1 rounded-full text-xs font-medium bg-dark-800 text-dark-300 border border-dark-700 hover:border-accent-500/50 hover:text-white transition-colors">
                                {{ $genre->name }}
                            </a>
                        @endforeach
                    </div>
                @endif

                {{-- Synopsis --}}
                @if($movie->synopsis)
                    <div>
                        <h3 class="font-display font-semibold text-white mb-2">Nội dung</h3>
                        <p class="text-dark-300 leading-relaxed">{{ $movie->synopsis }}</p>
                    </div>
                @endif

                {{-- Trailer --}}
                @if($movie->trailer_url)
                    <a href="{{ $movie->trailer_url }}" target="_blank" class="btn-secondary !inline-flex text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Xem Trailer
                    </a>
                @endif
            </div>
        </div>

        {{-- Directors & Writers --}}
        @if($directors->isNotEmpty() || $writers->isNotEmpty())
            <div class="card p-6 mb-8">
                <div class="grid sm:grid-cols-2 gap-6">
                    @if($directors->isNotEmpty())
                        <div>
                            <h3 class="font-display font-semibold text-white text-sm mb-2">🎬 Đạo diễn</h3>
                            <p class="text-dark-300 text-sm">{{ $directors->pluck('name')->join(', ') }}</p>
                        </div>
                    @endif
                    @if($writers->isNotEmpty())
                        <div>
                            <h3 class="font-display font-semibold text-white text-sm mb-2">✍️ Biên kịch</h3>
                            <p class="text-dark-300 text-sm">{{ $writers->pluck('name')->join(', ') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Cast --}}
        @if($cast->isNotEmpty())
            <section class="mb-12">
                <h2 class="text-xl font-display font-bold text-white mb-4">🎭 Diễn viên</h2>
                <div class="flex gap-4 overflow-x-auto pb-4 scrollbar-hide">
                    @foreach($cast as $person)
                        <div class="shrink-0 w-28 text-center">
                            <div class="w-28 h-28 rounded-xl bg-dark-800 overflow-hidden mb-2">
                                @if($person->photo)
                                    <img src="{{ $person->photo }}" alt="{{ $person->name }}" class="w-full h-full object-cover"
                                        loading="lazy">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-dark-500">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <p class="text-white text-xs font-medium line-clamp-1">{{ $person->name }}</p>
                            @if($person->pivot->character_name)
                                <p class="text-dark-400 text-xs line-clamp-1">{{ $person->pivot->character_name }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Review Form --}}
        @auth
            <section class="mb-12" id="review-form">
                <h2 class="text-xl font-display font-bold text-white mb-4">✏️ Viết đánh giá</h2>

                @if(session('success'))
                    <div class="p-4 rounded-xl bg-green-500/10 border border-green-500/20 text-green-400 text-sm mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('reviews.store', $movie) }}" class="card p-6 space-y-4">
                    @csrf

                    {{-- Rating --}}
                    <div>
                        <label class="block text-sm font-medium text-dark-200 mb-2">Điểm đánh giá <span
                                class="text-accent-400">*</span></label>
                        <div class="flex items-center gap-3" x-data="{ rating: 7 }">
                            <input type="range" name="rating" min="1" max="10" step="0.5" x-model="rating"
                                class="flex-1 h-2 bg-dark-700 rounded-lg appearance-none cursor-pointer accent-accent-500">
                            <div class="w-12 h-12 rounded-xl bg-accent-500 flex items-center justify-center shrink-0">
                                <span class="text-white font-bold" x-text="parseFloat(rating).toFixed(1)"></span>
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('rating')" class="mt-1" />
                    </div>

                    {{-- Title (optional) --}}
                    <div>
                        <label for="review-title" class="block text-sm font-medium text-dark-200 mb-2">Tiêu đề <span
                                class="text-dark-500">(không bắt buộc)</span></label>
                        <input id="review-title" type="text" name="title" value="{{ old('title') }}" class="input-dark"
                            placeholder="Tóm tắt cảm nhận trong 1 câu...">
                    </div>

                    {{-- Content (optional) --}}
                    <div>
                        <label for="review-content" class="block text-sm font-medium text-dark-200 mb-2">Nội dung review
                            <span class="text-dark-500">(không bắt buộc — bỏ trống = quick rating)</span></label>
                        <textarea id="review-content" name="content" rows="4" class="input-dark resize-y"
                            placeholder="Chia sẻ cảm nhận chi tiết của bạn...">{{ old('content') }}</textarea>
                    </div>

                    {{-- Spoiler --}}
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_spoiler" value="1"
                            class="w-4 h-4 rounded bg-dark-700 border-dark-500 text-accent-500 focus:ring-accent-500 focus:ring-offset-dark-900 cursor-pointer">
                        <span class="text-sm text-dark-300">Có chứa nội dung spoiler</span>
                    </label>

                    <button type="submit" class="btn-primary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Đăng đánh giá
                    </button>
                </form>
            </section>
        @else
            <div class="card p-6 text-center mb-12">
                <p class="text-dark-400 mb-3">Đăng nhập để viết đánh giá phim này</p>
                <a href="{{ route('login') }}" class="btn-primary text-sm">Đăng nhập</a>
            </div>
        @endauth

        {{-- Reviews List --}}
        @if($movie->reviews->isNotEmpty())
            <section class="mb-12">
                <h2 class="text-xl font-display font-bold text-white mb-4">💬 Đánh giá ({{ $ratingCount }})</h2>
                <div class="space-y-4">
                    @foreach($movie->reviews as $review)
                        <div class="card p-5">
                            <div class="flex items-start gap-4">
                                <div
                                    class="w-10 h-10 rounded-full bg-dark-600 flex items-center justify-center overflow-hidden shrink-0">
                                    @if($review->user->avatar)
                                        <img src="{{ $review->user->avatar }}" class="w-full h-full object-cover">
                                    @else
                                        <span
                                            class="text-sm font-bold text-dark-300">{{ strtoupper(substr($review->user->name, 0, 1)) }}</span>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-3 mb-1">
                                        <span class="font-medium text-white text-sm">{{ $review->user->name }}</span>
                                        @if($review->rating)
                                            <div class="flex items-center gap-1 bg-dark-700/50 px-2 py-0.5 rounded-lg">
                                                <svg class="w-3.5 h-3.5 text-accent-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                                <span
                                                    class="text-white text-xs font-bold">{{ number_format($review->rating, 1) }}</span>
                                            </div>
                                        @endif
                                        @if($review->is_spoiler)
                                            <span class="text-xs text-red-400 bg-red-500/10 px-2 py-0.5 rounded-full">⚠️
                                                Spoiler</span>
                                        @endif
                                        <span class="text-dark-500 text-xs">{{ $review->published_at->diffForHumans() }}</span>
                                    </div>
                                    @if($review->title)
                                        <h4 class="font-semibold text-white text-sm mb-1">{{ $review->title }}</h4>
                                    @endif
                                    @if($review->content)
                                        <p class="text-dark-300 text-sm leading-relaxed">{{ $review->content }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Related Movies --}}
        @if($relatedMovies->isNotEmpty())
            <section>
                <h2 class="text-xl font-display font-bold text-white mb-4">🎬 Phim liên quan</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach($relatedMovies as $relatedMovie)
                        <x-movie-card :movie="$relatedMovie" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-app-layout>