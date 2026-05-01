<x-app-layout>
    <x-slot:title>{{ $tvShow->title }}</x-slot:title>

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
            @if ($tvShow->backdrop)
                <img src="{{ $tvShow->backdrop }}" alt="{{ $tvShow->title }}"
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
                        @if ($tvShow->poster)
                            <div class="rounded-2xl overflow-hidden shadow-2xl ring-4 ring-white">
                                <img src="{{ $tvShow->poster }}" alt="{{ $tvShow->title }}" class="w-full block">
                            </div>
                        @else
                            <div
                                class="w-full aspect-[2/3] rounded-2xl bg-gray-200 flex items-center justify-center shadow-2xl ring-4 ring-white">
                                <svg class="w-14 h-14 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                                </svg>
                            </div>
                        @endif
                    </div>

                    {{-- Details --}}
                    <div class="flex-1 pt-4 md:pt-6 relative z-10">

                        {{-- Title (white while still over hero gradient) --}}
                        <div class="mb-3">
                            <h1 class="text-3xl lg:text-4xl font-display font-bold text-white drop-shadow-lg">
                                {{ $tvShow->title }}</h1>
                            @if ($tvShow->original_title && $tvShow->original_title !== $tvShow->title)
                                <p class="text-gray-300 text-sm italic mt-1 drop-shadow">{{ $tvShow->original_title }}
                                </p>
                            @endif
                        </div>

                        @if ($tvShow->tagline)
                            <p class="text-sky-300 font-medium italic text-base mb-4 drop-shadow">
                                "{{ $tvShow->tagline }}"</p>
                        @endif

                        {{-- Meta Info --}}
                        <div class="flex items-center flex-wrap gap-3 text-sm mb-4">
                            @if ($tvShow->release_date)
                                <span
                                    class="flex items-center gap-1.5 bg-black/30 backdrop-blur-sm text-white px-3 py-1.5 rounded-full border border-white/20">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ \Carbon\Carbon::parse($tvShow->release_date)->format('d/m/Y') }}
                                </span>
                            @endif
                            @if ($tvShow->runtime)
                                @php
                                    $h = intdiv($tvShow->runtime, 60);
                                    $m = $tvShow->runtime % 60;
                                @endphp
                                <span
                                    class="flex items-center gap-1.5 bg-black/30 backdrop-blur-sm text-white px-3 py-1.5 rounded-full border border-white/20">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $h > 0 ? $h . ' tiếng ' : '' }}{{ $m > 0 ? $m . ' phút' : '' }}
                                </span>
                            @endif
                            @if ($tvShow->country)
                                <span
                                    class="flex items-center gap-1.5 bg-black/30 backdrop-blur-sm text-white px-3 py-1.5 rounded-full border border-white/20">
                                    🌍 {{ $countryName }}
                                </span>
                            @endif
                        </div>

                        {{-- Genres --}}
                        @if ($tvShow->genres->isNotEmpty())
                            <div class="flex flex-wrap gap-2 mb-6">
                                @foreach ($tvShow->genres as $genre)
                                    <a href="{{ route('explore', ['genre' => $genre->id]) }}"
                                        class="px-3 py-1 text-sm font-medium bg-sky-50 text-sky-600 border border-sky-200 rounded-full hover:bg-sky-100 transition-colors">
                                        {{ $genre->name }}
                                    </a>
                                @endforeach
                            </div>
                        @endif

                        {{-- Rating + Actions --}}
                        @php
                            $topMoods = \App\Models\TvShowVibe::where('tv_show_id', $tvShow->id)
                                ->whereNotNull('mood')
                                ->groupBy('mood')
                                ->select('mood', \DB::raw('count(*) as count'))
                                ->orderByDesc('count')
                                ->limit(3)
                                ->pluck('mood')
                                ->toArray();
                            $myVibe = auth()->check()
                                ? \App\Models\TvShowVibe::where('tv_show_id', $tvShow->id)
                                    ->where('user_id', auth()->id())
                                    ->first()
                                : null;
                            $myMood = $myVibe->mood ?? null;
                            $myTone = $myVibe->tone ?? null;
                        @endphp

                        <div class="flex flex-col gap-4" x-data="{
                            topMoods: @js($topMoods),
                            myMood: @js($myMood),
                            isFavorited: {{ auth()->check() && $tvShow->favoritedBy->contains(auth()->id()) ? 'true' : 'false' }},
                            watchlistStatus: '{{ auth()->check() ? $tvShow->watchlistedBy->where('id', auth()->id())->first()?->pivot?->status ?? '' : '' }}',
                            getTwemojiUrl(emoji) {
                                if (!emoji) return '';
                                const codePoints = Array.from(emoji).map(c => c.codePointAt(0).toString(16));
                                const cleanCodePoints = codePoints.filter(cp => cp !== 'fe0f').join('-');
                                return `https://cdnjs.cloudflare.com/ajax/libs/twemoji/14.0.2/svg/${cleanCodePoints}.svg`;
                            },
                            async toggleFavorite() {
                                @guest window.location.href = '{{ route('login') }}';
                                return;
                                @endguest
                                const res = await fetch('{{ route('favorites.toggle') }}', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                    body: JSON.stringify({ tv_show_id: {{ $tvShow->id }} })
                                });
                                const data = await res.json();
                                if (data.success) this.isFavorited = data.is_favorited;
                            },
                            async updateWatchlist(status) {
                                @guest window.location.href = '{{ route('login') }}';
                                return;
                                @endguest
                                const res = await fetch('{{ route('watchlist.toggle') }}', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                    body: JSON.stringify({ tv_show_id: {{ $tvShow->id }}, status: status })
                                });
                                const data = await res.json();
                                if (data.success) this.watchlistStatus = data.in_watchlist ? data.status : '';
                            }
                        }"
                            @vibes-updated.window="topMoods = [...$event.detail.top_moods]; myMood = $event.detail.mood;">

                            {{-- Row 1: Score + Vibe --}}
                            <div class="flex flex-wrap items-center gap-4">

                                {{-- User Score Widget (TMDb-style) --}}
                                @php
                                    $pct = $avgRating > 0 ? round($avgRating * 10) : 0;
                                    $circumference = 2 * M_PI * 20; // r=20
                                    $dashOffset = $circumference * (1 - $pct / 100);
                                    $ringColor = match (true) {
                                        $pct >= 90 => '#eab308', // yellow
                                        $pct >= 70 => '#14b8a6', // teal
                                        $pct >= 50 => '#f97316', // orange
                                        $pct > 0 => '#ef4444', // red
                                        default => '#64748b',
                                    };
                                    $distJson = json_encode(array_values($distribution));
                                    $maxDist = max(array_values($distribution) ?: [0]);
                                    $historyJson = json_encode(array_values($ratingHistory));
                                @endphp

                                <div x-data="userScore({
                                    pct: {{ $pct }},
                                    avg: {{ $avgRating ?: 0 }},
                                    count: {{ $ratingCount }},
                                    dist: {{ $distJson }},
                                    history: {{ $historyJson }},
                                    circumference: {{ round($circumference, 2) }}
                                })" class="relative">

                                    {{-- Trigger Button --}}
                                    <button @click="open = !open"
                                        class="group flex items-center gap-3 bg-white hover:bg-gray-50 rounded-2xl shadow-md border border-gray-200 hover:border-gray-300 px-4 py-2.5 hover:shadow-lg transition-all duration-200 cursor-pointer"
                                        :class="open ? 'ring-2 ring-gray-200 shadow-lg bg-gray-50' : ''">

                                        {{-- SVG Circle (dark bg like TMDb) --}}
                                        <div class="relative w-12 h-12 shrink-0">
                                            <svg class="w-full h-full -rotate-90" viewBox="0 0 48 48">
                                                <circle cx="24" cy="24" r="20" fill="#1e293b"
                                                    stroke="#334155" stroke-width="4" />
                                                <circle cx="24" cy="24" r="20" fill="none"
                                                    stroke="{{ $ringColor }}" stroke-width="4"
                                                    stroke-linecap="round"
                                                    stroke-dasharray="{{ round($circumference, 2) }}"
                                                    stroke-dashoffset="{{ $avgRating > 0 ? round($dashOffset, 2) : round($circumference, 2) }}"
                                                    style="transition: stroke-dashoffset 0.8s ease" />
                                            </svg>
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                @if ($avgRating > 0)
                                                    <span class="text-[11px] font-black leading-none"
                                                        style="color: {{ $ringColor }}">{{ $pct }}<sup
                                                            class="text-[7px]">%</sup></span>
                                                @else
                                                    <span class="text-[9px] text-slate-400 font-semibold">N/A</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="text-left">
                                            <p class="text-[10px] font-bold text-gray-600 uppercase tracking-widest">
                                                Điểm
                                                cộng đồng</p>
                                            @if ($avgRating > 0)
                                                <p class="text-sm font-black text-gray-900 leading-tight">
                                                    {{ number_format($avgRating, 1) }}<span
                                                        class="text-xs text-gray-500 font-normal">/10</span></p>
                                                <p class="text-[10px] text-gray-500">{{ number_format($ratingCount) }}
                                                    đánh
                                                    giá</p>
                                            @else
                                                <p class="text-xs text-gray-500">Chưa có đánh giá</p>
                                            @endif
                                        </div>

                                        {{-- Chevron → xoay 90° khi mở --}}
                                        <svg class="w-3.5 h-3.5 text-gray-400 ml-0.5 transition-transform duration-200"
                                            :class="open ? 'rotate-90' : ''" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>

                                    {{-- Score Panel — slides up from BOTTOM --}}
                                    <div x-show="open" x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 translate-y-3 scale-[0.97]"
                                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                        x-transition:leave="transition ease-in duration-150"
                                        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                        x-transition:leave-end="opacity-0 translate-y-3 scale-[0.97]"
                                        @click.outside="open = false"
                                        class="absolute left-full bottom-0 ml-3 w-80 bg-white border border-gray-100 rounded-2xl shadow-2xl z-[200] flex flex-col"
                                        style="display:none; max-height: min(540px, 90vh);">

                                        {{-- Panel Header (no duplicate circle) --}}
                                        <div class="px-5 pt-5 pb-4 border-b border-gray-100 shrink-0">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="min-w-0">
                                                    <p
                                                        class="text-[10px] font-bold text-gray-600 uppercase tracking-widest mb-1">
                                                        Phân tích điểm</p>
                                                    @if ($avgRating > 0)
                                                        <div class="flex items-center gap-3 flex-wrap mt-0.5">
                                                            <div class="flex items-baseline gap-0.5">
                                                                <span
                                                                    class="text-3xl font-black text-gray-900 leading-none">{{ number_format($avgRating, 2) }}</span>
                                                                <span
                                                                    class="text-sm text-gray-500 font-medium">/10</span>
                                                            </div>
                                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                                                            <span class="text-2xl font-black tracking-tight"
                                                                style="color: {{ $ringColor }}">{{ $pct }}%</span>
                                                        </div>
                                                        <p class="text-xs text-gray-500 mt-1.5">Dựa trên <strong
                                                                class="text-gray-800">{{ number_format($ratingCount) }}</strong>
                                                            đánh giá</p>
                                                    @else
                                                        <p class="text-gray-500 text-sm">Chưa có đánh giá nào</p>
                                                    @endif
                                                </div>
                                                <button @click="open = false"
                                                    class="shrink-0 w-7 h-7 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors mt-0.5">
                                                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Scrollable body --}}
                                        <div class="overflow-y-auto flex-1" style="overscroll-behavior: contain;">

                                            @if ($avgRating > 0)

                                                {{-- Distribution Bar Chart --}}
                                                <div
                                                    class="px-5 py-4{{ count($ratingHistory) > 1 ? ' border-b border-gray-50' : '' }}">
                                                    <p
                                                        class="text-[10px] font-bold text-gray-600 uppercase tracking-widest mb-3">
                                                        Phân phối điểm</p>
                                                    <div class="space-y-2">
                                                        @foreach (array_reverse(range(1, 10), true) as $score)
                                                            @php
                                                                $cnt = $distribution[$score] ?? 0;
                                                                $barW =
                                                                    $maxDist > 0 ? round(($cnt / $maxDist) * 100) : 0;
                                                                $bColor = match (true) {
                                                                    $score >= 9 => '#eab308',
                                                                    $score >= 7 => '#14b8a6',
                                                                    $score >= 5 => '#f97316',
                                                                    default => '#ef4444',
                                                                };
                                                            @endphp
                                                            <div class="flex items-center gap-2.5">
                                                                <span
                                                                    class="text-[10px] font-bold text-gray-600 w-3 text-right shrink-0">{{ $score }}</span>
                                                                <div
                                                                    class="flex-1 bg-gray-200 rounded-full h-2 overflow-hidden">
                                                                    <div class="h-full rounded-full"
                                                                        style="width: {{ $barW }}%; background: {{ $bColor }}; transition: width 0.6s ease;">
                                                                    </div>
                                                                </div>
                                                                <span
                                                                    class="text-[10px] font-semibold text-gray-600 w-4 shrink-0 text-right">{{ $cnt }}</span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                {{-- Score History Sparkline --}}
                                                @if (count($ratingHistory) > 1)
                                                    <div class="px-5 py-4">
                                                        <p
                                                            class="text-[10px] font-bold text-gray-600 uppercase tracking-widest mb-3">
                                                            Lịch sử 12 tháng</p>
                                                        <div id="us-sparkline" class="w-full rounded-lg bg-gray-50"
                                                            style="height:68px;"></div>
                                                        <div class="flex justify-between mt-1.5">
                                                            <span
                                                                class="text-[9px] text-gray-500">{{ array_key_first($ratingHistory) }}</span>
                                                            <span
                                                                class="text-[9px] text-gray-500">{{ array_key_last($ratingHistory) }}</span>
                                                        </div>
                                                    </div>
                                                @endif
                                            @else
                                                {{-- Empty state --}}
                                                <div class="px-5 py-8 text-center">
                                                    <svg class="w-10 h-10 text-gray-200 mx-auto mb-3" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="1.5"
                                                            d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                                    </svg>
                                                    <p class="text-sm text-gray-500 font-semibold mb-1">Chưa có đánh
                                                        giá
                                                    </p>
                                                    <a href="#review-form" @click="open=false"
                                                        class="text-xs text-sky-500 hover:text-sky-600 font-semibold">
                                                        Hãy là người đầu tiên →
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Top Emojis & Vibe Trigger --}}
                                <div class="flex items-center gap-4 ml-[15px]">
                                    {{-- Overlapping Top Emojis --}}
                                    <div class="flex items-center -space-x-3" x-show="topMoods && topMoods.length > 0"
                                        x-cloak>
                                        <template x-for="(emj, idx) in topMoods" :key="emj">
                                            <div class="w-11 h-11 rounded-full bg-[#1e293b] border-[3px] border-[#0f172a] flex items-center justify-center shadow-lg relative"
                                                :style="`z-index: ${3 - idx}`">
                                                <img :src="getTwemojiUrl(emj)" class="w-7 h-7 drop-shadow-md">
                                            </div>
                                        </template>
                                    </div>

                                    {{-- Vibe Button (TMDb style dark blue pill) --}}
                                    <button @click="$dispatch('open-vibe-modal')"
                                        class="group flex items-center gap-2 bg-[#022541] hover:bg-[#03345a] text-white rounded-full px-5 py-2.5 transition-colors shadow-lg">
                                        <span x-show="myMood" class="font-bold text-base flex items-center gap-2"
                                            @if (!$myMood) x-cloak @endif>
                                            <span>Vibe của bạn</span>
                                            <img :src="getTwemojiUrl(myMood)" class="w-6 h-6 self-center">
                                        </span>
                                        <div x-show="!myMood" class="flex items-center gap-1"
                                            @if ($myMood) x-cloak @endif>
                                            <span class="font-bold text-base whitespace-nowrap"><span
                                                    class="border-b-[3px] border-sky-400 pb-0.5">Vibe</span> của
                                                bạn?</span>
                                            <svg class="w-5 h-5 text-white/90 group-hover:scale-110 transition-transform"
                                                fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </div>

                            </div> {{-- End Row 1 --}}

                            {{-- Row 2: Actions --}}
                            <div class="flex flex-wrap items-center gap-4">

                                {{-- Actions --}}
                                @if ($tvShow->trailer_url)
                                    <button @click="openTrailer('{{ $tvShow->trailer_url }}')"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg shadow-sky-600/30">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Trailer
                                    </button>
                                @endif

                                <a href="#review-form"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-xl border border-gray-200 transition-all duration-200 shadow-sm">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                    Viết review
                                </a>

                                {{-- Favorite --}}
                                <button @click="toggleFavorite()" title="Yêu thích"
                                    class="p-2.5 rounded-xl border transition-all flex items-center justify-center h-[46px] w-[46px] {{ auth()->check() && $tvShow->favoritedBy->contains(auth()->id()) ? 'bg-rose-50 border-rose-300 text-rose-500' : 'bg-white border-gray-200 text-gray-400 shadow-sm' }}"
                                    :class="isFavorited ? 'bg-rose-50 border-rose-300 text-rose-500' :
                                        'bg-white text-gray-400 hover:text-rose-500 hover:border-rose-300 shadow-sm border-gray-200'">
                                    <svg class="w-5 h-5" :fill="isFavorited ? 'currentColor' : 'none'"
                                        fill="{{ auth()->check() && $tvShow->favoritedBy->contains(auth()->id()) ? 'currentColor' : 'none' }}"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                </button>

                                {{-- Share Button & Web Popup --}}
                                <div x-data="{ showSharePopup: false }">
                                    <button @click="navigator.clipboard.writeText(window.location.href).then(() => { showSharePopup = true; setTimeout(() => showSharePopup = false, 3000); })"
                                        title="Chia sẻ"
                                        class="p-2.5 rounded-xl border bg-white border-gray-200 text-gray-500 hover:text-sky-600 hover:border-sky-300 hover:bg-sky-50 transition-all shadow-sm flex items-center justify-center h-[46px] w-[46px]">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                        </svg>
                                    </button>

                                    {{-- Web Toast Popup --}}
                                    <template x-teleport="body">
                                        <div x-show="showSharePopup" 
                                            x-transition:enter="transition ease-out duration-300"
                                            x-transition:enter-start="opacity-0 translate-y-4"
                                            x-transition:enter-end="opacity-100 translate-y-0"
                                            x-transition:leave="transition ease-in duration-200"
                                            x-transition:leave-start="opacity-100 translate-y-0"
                                            x-transition:leave-end="opacity-0 translate-y-4"
                                            class="fixed bottom-6 right-6 z-[100] flex items-center gap-3 px-5 py-4 bg-gray-900 border border-gray-700/50 rounded-xl shadow-2xl backdrop-blur-sm"
                                            style="display: none;">
                                            <svg class="w-5 h-5 text-green-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            <p class="text-sm font-medium text-white">Đã sao chép liên kết TV Series!</p>
                                            <button @click="showSharePopup = false" class="ml-2 text-gray-400 hover:text-white transition-colors shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>

                                {{-- Watchlist Dropdown --}}
                                @php
                                    $wlStatus = auth()->check()
                                        ? $tvShow->watchlistedBy->where('id', auth()->id())->first()?->pivot?->status ??
                                            ''
                                        : '';
                                    $wlClass = match ($wlStatus) {
                                        'want_to_watch' => 'bg-sky-50 border-sky-400 text-sky-700',
                                        'watching' => 'bg-amber-50 border-amber-400 text-amber-700',
                                        'watched' => 'bg-emerald-50 border-emerald-400 text-emerald-700',
                                        'dropped' => 'bg-rose-50 border-rose-400 text-rose-700',
                                        default => 'bg-white border-gray-200 text-gray-600',
                                    };
                                    $wlText = match ($wlStatus) {
                                        'want_to_watch' => 'Muốn xem',
                                        'watching' => 'Đang xem',
                                        'watched' => 'Đã xem',
                                        'dropped' => 'Bỏ dở',
                                        default => 'Watchlist',
                                    };
                                @endphp
                                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                                    <button @click="open = !open"
                                        class="flex items-center gap-2 px-4 py-2.5 rounded-xl border text-sm font-medium transition-all {{ $wlClass }}"
                                        :class="{
                                            'bg-sky-50 border-sky-400 text-sky-700': watchlistStatus === 'want_to_watch',
                                            'bg-amber-50 border-amber-400 text-amber-700': watchlistStatus === 'watching',
                                            'bg-emerald-50 border-emerald-400 text-emerald-700': watchlistStatus === 'watched',
                                            'bg-rose-50 border-rose-400 text-rose-700': watchlistStatus === 'dropped',
                                            'bg-white border-gray-200 text-gray-600 hover:border-gray-300': !
                                                watchlistStatus
                                        }">
                                        <svg class="w-4 h-4" :fill="watchlistStatus ? 'currentColor' : 'none'"
                                            fill="{{ $wlStatus ? 'currentColor' : 'none' }}" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                                        </svg>
                                        <span
                                            x-text="watchlistStatus === 'want_to_watch' ? 'Muốn xem' : (watchlistStatus === 'watching' ? 'Đang xem' : (watchlistStatus === 'watched' ? 'Đã xem' : (watchlistStatus === 'dropped' ? 'Bỏ dở' : 'Watchlist')))">{{ $wlText }}</span>
                                        <svg class="w-3.5 h-3.5 opacity-50" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    <div x-show="open" x-transition.opacity.duration.150ms
                                        class="absolute left-0 mt-2 w-48 bg-white border border-gray-100 rounded-xl shadow-xl overflow-hidden z-20 p-1"
                                        style="display: none;">
                                        <button @click="updateWatchlist('want_to_watch'); open = false"
                                            class="w-full flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition-colors text-left"
                                            :class="watchlistStatus === 'want_to_watch' ?
                                                'text-sky-700 bg-sky-100 font-semibold' :
                                                'text-gray-700 hover:bg-sky-50 hover:text-sky-600'">
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                                            </svg>
                                            Muốn xem
                                        </button>
                                        <button @click="updateWatchlist('watching'); open = false"
                                            class="w-full flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition-colors text-left"
                                            :class="watchlistStatus === 'watching' ?
                                                'text-amber-700 bg-amber-100 font-semibold' :
                                                'text-gray-700 hover:bg-amber-50 hover:text-amber-600'">
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Đang xem
                                        </button>
                                        <button @click="updateWatchlist('watched'); open = false"
                                            class="w-full flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition-colors text-left"
                                            :class="watchlistStatus === 'watched' ?
                                                'text-emerald-700 bg-emerald-100 font-semibold' :
                                                'text-gray-700 hover:bg-emerald-50 hover:text-emerald-600'">
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Đã xem
                                        </button>
                                        <button @click="updateWatchlist('dropped'); open = false"
                                            class="w-full flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition-colors text-left"
                                            :class="watchlistStatus === 'dropped' ?
                                                'text-rose-700 bg-rose-100 font-semibold' :
                                                'text-gray-700 hover:bg-rose-50 hover:text-rose-600'">
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Bỏ dở
                                        </button>
                                        <button x-show="watchlistStatus"
                                            @click="updateWatchlist(watchlistStatus); open = false"
                                            class="w-full flex items-center gap-2 px-3 py-2 text-sm text-gray-400 hover:bg-gray-50 hover:text-gray-600 transition border-t border-gray-100 mt-1 rounded-lg">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Gỡ khỏi danh sách
                                        </button>
                                    </div>
                                </div>
                            </div> {{-- End Row 2 --}}

                            {{-- Include Modal --}}
                            <x-tv-show-vibe-modal :tvShow="$tvShow" :myMood="$myMood" :myTone="$myTone" />

                        </div>
                    </div>
                </div>

                {{-- ══════════════════════════════════════════════════ --}}
                {{-- MAIN CONTENT GRID                                 --}}
                {{-- ══════════════════════════════════════════════════ --}}
                <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_260px] gap-8 pb-8">

                    {{-- Left Column (main content) --}}
                    <div class="space-y-8">

                        {{-- Synopsis --}}
                        @if ($tvShow->synopsis)
                            <section>
                                <h2 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2">
                                    <span class="w-1 h-5 bg-sky-500 rounded-full inline-block"></span>
                                    Nội dung
                                </h2>
                                <div class="text-gray-900 leading-relaxed text-sm">
                                    <x-expandable-text :text="$tvShow->synopsis" :maxLength="350" />
                                </div>
                            </section>
                        @endif

                        {{-- Directors & Writers --}}
                        @php
                            $directors = $creators->filter(fn($p) => $p->pivot->role === 'director');
                            $writers = $creators->filter(fn($p) => $p->pivot->role === 'writer');
                        @endphp
                        @if ($directors->isNotEmpty() || $writers->isNotEmpty())
                            <section>
                                <h2 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2">
                                    <span class="w-1 h-5 bg-sky-500 rounded-full inline-block"></span>
                                    Đội ngũ sản xuất
                                </h2>
                                <div class="grid sm:grid-cols-2 gap-4">
                                    @if ($directors->isNotEmpty())
                                        <div class="bg-gray-50 border border-gray-100 rounded-2xl p-4">
                                            <p
                                                class="text-xs font-semibold text-sky-500 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                </svg>
                                                Đạo diễn
                                            </p>
                                            <p class="text-gray-800 font-medium text-sm">
                                                {{ $directors->pluck('name')->join(', ') }}</p>
                                        </div>
                                    @endif
                                    @if ($writers->isNotEmpty())
                                        <div class="bg-gray-50 border border-gray-100 rounded-2xl p-4">
                                            <p
                                                class="text-xs font-semibold text-sky-500 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                                Biên kịch
                                            </p>
                                            <p class="text-gray-800 font-medium text-sm">
                                                {{ $writers->pluck('name')->join(', ') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </section>
                        @endif

                        {{-- Cast --}}
                        @if ($cast->isNotEmpty())
                            <section>
                                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                    <span class="w-1 h-5 bg-sky-500 rounded-full inline-block"></span>
                                    Diễn viên
                                </h2>
                                <div class="flex gap-4 overflow-x-auto pb-4 snap-x -mx-1 px-1">
                                    @foreach ($cast as $person)
                                        <a href="{{ route('person.show', $person) }}"
                                            class="shrink-0 w-[140px] snap-start bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow group">
                                            <div class="aspect-[2/3] w-full bg-gray-100 overflow-hidden relative">
                                                @if ($person->photo)
                                                    <img src="{{ $person->photo }}" alt="{{ $person->name }}"
                                                        class="w-full h-full object-cover" loading="lazy">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center">
                                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="p-3">
                                                <p class="text-[15px] font-bold text-gray-900 leading-snug group-hover:text-sky-600 transition-colors">
                                                    {{ $person->name }}
                                                </p>
                                                @if ($person->pivot->character_name)
                                                    <p class="text-[13px] text-gray-600 mt-0.5 leading-snug">
                                                        {{ $person->pivot->character_name }}
                                                    </p>
                                                @endif
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </section>
                        @endif

                        {{-- Media --}}
                        @if (count($media['videos']) > 0 || count($media['backdrops']) > 0 || count($media['posters']) > 0)
                            <section x-data="{ activeTab: 'popular' }" class="mb-8">
                                <div class="flex flex-wrap items-center gap-4 mb-4">
                                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                        <span class="w-1 h-5 bg-sky-500 rounded-full inline-block"></span>
                                        Media
                                    </h2>
                                    <div class="flex bg-gray-100 p-1 rounded-lg overflow-x-auto scrollbar-hide">
                                        <button @click="activeTab = 'popular'"
                                            :class="{ 'bg-white shadow text-gray-900': activeTab === 'popular', 'text-gray-500 hover:text-gray-700': activeTab !== 'popular' }"
                                            class="px-3 py-1.5 text-sm font-medium rounded-md transition-all shrink-0">
                                            Phổ biến nhất
                                        </button>
                                        @if (count($media['videos']) > 0)
                                            <button @click="activeTab = 'videos'"
                                                :class="{ 'bg-white shadow text-gray-900': activeTab === 'videos', 'text-gray-500 hover:text-gray-700': activeTab !== 'videos' }"
                                                class="px-3 py-1.5 text-sm font-medium rounded-md transition-all shrink-0">
                                                Videos <span class="text-xs text-gray-400 ml-1">{{ count($media['videos']) }}</span>
                                            </button>
                                        @endif
                                        @if (count($media['backdrops']) > 0)
                                            <button @click="activeTab = 'backdrops'"
                                                :class="{ 'bg-white shadow text-gray-900': activeTab === 'backdrops', 'text-gray-500 hover:text-gray-700': activeTab !== 'backdrops' }"
                                                class="px-3 py-1.5 text-sm font-medium rounded-md transition-all shrink-0">
                                                Backdrops <span class="text-xs text-gray-400 ml-1">{{ count($media['backdrops']) }}</span>
                                            </button>
                                        @endif
                                        @if (count($media['posters']) > 0)
                                            <button @click="activeTab = 'posters'"
                                                :class="{ 'bg-white shadow text-gray-900': activeTab === 'posters', 'text-gray-500 hover:text-gray-700': activeTab !== 'posters' }"
                                                class="px-3 py-1.5 text-sm font-medium rounded-md transition-all shrink-0">
                                                Posters <span class="text-xs text-gray-400 ml-1">{{ count($media['posters']) }}</span>
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <div class="min-h-[260px]">
                                    {{-- Most Popular --}}
                                    <div x-show="activeTab === 'popular'" class="flex gap-4 overflow-x-auto pb-4 snap-x -mx-1 px-1">
                                    {{-- 1 Top Video --}}
                                    @php
                                        $topVideo = collect($media['videos'])->firstWhere('site', 'YouTube');
                                    @endphp
                                    @if($topVideo)
                                        <div class="shrink-0 w-72 sm:w-80 snap-start">
                                            <div class="relative aspect-video rounded-xl overflow-hidden group cursor-pointer shadow-sm border border-gray-100"
                                                @click="openTrailer('https://www.youtube.com/watch?v={{ $topVideo['key'] }}')">
                                                <img src="https://img.youtube.com/vi/{{ $topVideo['key'] }}/mqdefault.jpg"
                                                    alt="{{ $topVideo['name'] }}"
                                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                                <div class="absolute inset-0 bg-black/20 flex items-center justify-center group-hover:bg-black/30 transition-colors">
                                                    <div class="w-12 h-12 bg-white/90 rounded-full flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                                        <svg class="w-5 h-5 text-sky-600 translate-x-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M4 4l12 6-12 6z" />
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- 2 Top Backdrops --}}
                                    @foreach (array_slice($media['backdrops'], 0, 2) as $image)
                                        <div class="shrink-0 w-72 sm:w-80 snap-start">
                                            <div class="aspect-video rounded-xl overflow-hidden shadow-sm border border-gray-100 bg-gray-100">
                                                <img src="https://image.tmdb.org/t/p/w780{{ $image['file_path'] }}"
                                                    class="w-full h-full object-cover" loading="lazy">
                                            </div>
                                        </div>
                                    @endforeach

                                    {{-- 2 Top Posters --}}
                                    @foreach (array_slice($media['posters'], 0, 2) as $image)
                                        <div class="shrink-0 w-36 sm:w-40 snap-start">
                                            <div class="aspect-[2/3] rounded-xl overflow-hidden shadow-sm border border-gray-100 bg-gray-100">
                                                <img src="https://image.tmdb.org/t/p/w342{{ $image['file_path'] }}"
                                                    class="w-full h-full object-cover" loading="lazy">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Videos --}}
                                @if (count($media['videos']) > 0)
                                    <div x-show="activeTab === 'videos'" class="flex gap-4 overflow-x-auto pb-4 snap-x -mx-1 px-1" style="display: none;">
                                        @foreach ($media['videos'] as $video)
                                            @if ($video['site'] === 'YouTube')
                                                <div class="shrink-0 w-72 sm:w-80 snap-start">
                                                    <div class="relative aspect-video rounded-xl overflow-hidden group cursor-pointer shadow-sm border border-gray-100"
                                                        @click="openTrailer('https://www.youtube.com/watch?v={{ $video['key'] }}')">
                                                        <img src="https://img.youtube.com/vi/{{ $video['key'] }}/mqdefault.jpg"
                                                            alt="{{ $video['name'] }}"
                                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                                        <div class="absolute inset-0 bg-black/20 flex items-center justify-center group-hover:bg-black/30 transition-colors">
                                                            <div class="w-12 h-12 bg-white/90 rounded-full flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                                                <svg class="w-5 h-5 text-sky-600 translate-x-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path d="M4 4l12 6-12 6z" />
                                                                </svg>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Backdrops --}}
                                @if (count($media['backdrops']) > 0)
                                    <div x-show="activeTab === 'backdrops'" class="flex gap-4 overflow-x-auto pb-4 snap-x -mx-1 px-1" style="display: none;">
                                        @foreach (array_slice($media['backdrops'], 0, 20) as $image)
                                            <div class="shrink-0 w-72 sm:w-80 snap-start">
                                                <div class="aspect-video rounded-xl overflow-hidden shadow-sm border border-gray-100 bg-gray-100">
                                                    <img src="https://image.tmdb.org/t/p/w780{{ $image['file_path'] }}"
                                                        class="w-full h-full object-cover" loading="lazy">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Posters --}}
                                @if (count($media['posters']) > 0)
                                    <div x-show="activeTab === 'posters'" class="flex gap-4 overflow-x-auto pb-4 snap-x -mx-1 px-1" style="display: none;">
                                        @foreach (array_slice($media['posters'], 0, 20) as $image)
                                            <div class="shrink-0 w-36 sm:w-40 snap-start">
                                                <div class="aspect-[2/3] rounded-xl overflow-hidden shadow-sm border border-gray-100 bg-gray-100">
                                                    <img src="https://image.tmdb.org/t/p/w342{{ $image['file_path'] }}"
                                                        class="w-full h-full object-cover" loading="lazy">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                </div>
                            </section>
                        @endif

                        {{-- Reviews List --}}
                        @if ($tvShow->reviews->isNotEmpty())
                            <section>
                                <div class="flex items-center justify-between mb-4">
                                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                        <span class="w-1 h-5 bg-sky-500 rounded-full inline-block"></span>
                                        Đánh giá cộng đồng
                                    </h2>
                                    <span
                                        class="text-xs font-semibold text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">{{ count($tvShow->reviews) }}
                                        reviews</span>
                                </div>
                                <div class="grid md:grid-cols-2 gap-4 items-start">
                                    @foreach ($tvShow->reviews as $review)
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
                            <x-review-form :movie="$tvShow" />
                        </section>

                    </div>

                    {{-- Right Column (sidebar info) --}}
                    <div class="space-y-6">

                        {{-- Movie Facts Card (TMDB-style compact) --}}
                        <div class="bg-transparent lg:bg-gray-50 lg:border lg:border-gray-100 rounded-2xl lg:p-5">
                            <h3 class="hidden lg:block text-sm font-bold text-gray-900 mb-6 uppercase tracking-wider">Thông tin</h3>
                            <dl class="space-y-5">
                                @if ($tvShow->original_title && $tvShow->original_title !== $tvShow->title)
                                    <div>
                                        <dt class="text-[15px] font-bold text-gray-900 mb-0.5">Tên gốc</dt>
                                        <dd class="text-[15px] text-gray-800 font-normal leading-snug">{{ $tvShow->original_title }}</dd>
                                    </div>
                                @endif

                                @if ($tvShow->tmdb_status)
                                    <div>
                                        <dt class="text-[15px] font-bold text-gray-900 mb-0.5">Trạng thái</dt>
                                        <dd class="text-[15px] text-gray-800 font-normal leading-snug">{{ $tvShow->tmdb_status }}</dd>
                                    </div>
                                @endif

                                @if (!empty($tvShow->networks))
                                    <div>
                                        <dt class="text-[15px] font-bold text-gray-900 mb-0.5">Mạng lưới</dt>
                                        <dd class="text-[15px] text-gray-800 font-normal leading-snug">
                                            @if (is_array($tvShow->networks))
                                                @foreach ($tvShow->networks as $network)
                                                    {{ is_array($network) ? $network['name'] ?? '' : $network }}
                                                    @if (!$loop->last) , @endif
                                                @endforeach
                                            @else
                                                {{ (string) $tvShow->networks }}
                                            @endif
                                        </dd>
                                    </div>
                                @endif

                                @if ($tvShow->language)
                                    <div>
                                        <dt class="text-[15px] font-bold text-gray-900 mb-0.5">Ngôn ngữ gốc</dt>
                                        <dd class="text-[15px] text-gray-800 font-normal leading-snug">{{ $languageName }}</dd>
                                    </div>
                                @endif

                                @if ($tvShow->number_of_seasons)
                                    <div>
                                        <dt class="text-[15px] font-bold text-gray-900 mb-0.5">Số mùa</dt>
                                        <dd class="text-[15px] text-gray-800 font-normal leading-snug">{{ $tvShow->number_of_seasons }}</dd>
                                    </div>
                                @endif

                                @if ($tvShow->number_of_episodes)
                                    <div>
                                        <dt class="text-[15px] font-bold text-gray-900 mb-0.5">Số tập</dt>
                                        <dd class="text-[15px] text-gray-800 font-normal leading-snug">{{ $tvShow->number_of_episodes }}</dd>
                                    </div>
                                @endif

                                @if ($tvShow->tags->isNotEmpty())
                                    <div>
                                        <dt class="text-[15px] font-bold text-gray-900 mb-2">Từ khóa</dt>
                                        <dd class="flex flex-wrap gap-1.5 mt-1">
                                            @foreach ($tvShow->tags as $tag)
                                                <span class="inline-block px-2.5 py-1 text-[13px] font-normal text-gray-800 bg-gray-200/80 hover:bg-gray-300 rounded-md transition-colors cursor-default">{{ $tag->name }}</span>
                                            @endforeach
                                        </dd>
                                    </div>
                                @endif
                            </dl>
                        </div>

                    </div>
                </div>

                {{-- Related Tv Shows --}}
                @if ($relatedTvShows->isNotEmpty())
                    <div class="border-t border-gray-100 pt-10 pb-10">
                        <x-tv-show-section title="Series liên quan" :items="$relatedTvShows" layout="grid" />
                    </div>
                @endif

            </div>
        </div>

        {{-- Trailer Modal --}}
        <x-trailer-modal />
    </div>
</x-app-layout>

<script>
    function userScore(config) {
        return {
            open: false,
            pct: config.pct,
            avg: config.avg,
            count: config.count,
            dist: config.dist,
            history: config.history,
            circumference: config.circumference,

            init() {
                this.$watch('open', val => {
                    if (val) this.$nextTick(() => this.drawSparkline());
                });
            },

            drawSparkline() {
                const container = document.getElementById('us-sparkline');
                if (!container || !this.history || this.history.length < 2) return;
                const data = this.history;
                const W = container.offsetWidth || 280,
                    H = 68;
                const scores = data.map(d => parseFloat(d.avg_score));
                const months = data.map(d => d.month);
                const minV = Math.max(0, Math.min(...scores) - 1);
                const maxV = Math.min(10, Math.max(...scores) + 0.5);
                const n = scores.length;
                const xStep = n > 1 ? (W - 20) / (n - 1) : W;
                const toY = s => H - 6 - ((s - minV) / (maxV - minV || 1)) * (H - 14);
                const toX = i => 10 + i * xStep;
                const pts = scores.map((s, i) => `${toX(i)},${toY(s)}`).join(' ');
                const areaBot = `${toX(n-1)},${H} ${toX(0)},${H}`;
                container.innerHTML = `<svg width="${W}" height="${H}" viewBox="0 0 ${W} ${H}" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="sg2" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#14b8a6" stop-opacity="0.35"/>
                        <stop offset="100%" stop-color="#14b8a6" stop-opacity="0"/>
                    </linearGradient>
                </defs>
                <polygon points="${pts} ${areaBot}" fill="url(#sg2)"/>
                <polyline points="${pts}" fill="none" stroke="#14b8a6" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"/>
                ${scores.map((s, i) => `<circle cx="${toX(i)}" cy="${toY(s)}" r="3" fill="#14b8a6"><title>${months[i]}: ${s.toFixed(1)}</title></circle>`).join('')}
            </svg>`;
            }
        };
    }
</script>
