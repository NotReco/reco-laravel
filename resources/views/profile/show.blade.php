<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Profile Header Card --}}
            <div
                class="bg-white/80 backdrop-blur-xl rounded-3xl border border-white/40 shadow-[0_8px_30px_rgb(0,0,0,0.04)] pb-6 md:pb-10 mb-4 relative overflow-hidden">

                {{-- Cover Photo Section --}}
                @if ($user->cover_photo)
                    <div class="w-full h-48 md:h-72 bg-gray-100 relative">
                        <img src="{{ Str::startsWith($user->cover_photo, 'http') ? $user->cover_photo : asset($user->cover_photo) }}"
                            alt="Cover Photo" class="w-full h-full object-cover">
                        {{-- Gradient fade to blend into the main card --}}
                        <div
                            class="absolute inset-x-0 bottom-0 h-16 md:h-24 bg-gradient-to-t from-white/90 to-transparent">
                        </div>
                    </div>
                @else
                    {{-- Default Decorative Background Section --}}
                    <div class="w-full h-32 md:h-48 relative overflow-hidden">
                        <div
                            class="absolute top-0 right-0 w-[500px] h-[500px] md:w-[700px] md:h-[700px] bg-gradient-to-bl from-sky-400/30 via-indigo-400/20 to-transparent rounded-full blur-[80px] -translate-y-1/3 translate-x-1/4 pointer-events-none">
                        </div>
                        <div
                            class="absolute bottom-0 left-0 w-80 h-80 bg-gradient-to-tr from-amber-300/10 via-rose-300/10 to-transparent rounded-full blur-[60px] translate-y-1/3 -translate-x-1/3 pointer-events-none">
                        </div>
                        <div
                            class="absolute inset-x-0 bottom-0 h-12 md:h-16 bg-gradient-to-t from-white/90 to-transparent">
                        </div>
                    </div>
                @endif

                <div
                    class="px-6 md:px-10 relative z-10 flex flex-col md:flex-row items-center md:items-start gap-6 md:gap-10 -mt-12 md:-mt-20">
                    {{-- Avatar with Frame overlay --}}
                    <div class="relative w-32 h-32 md:w-40 md:h-40 shrink-0">
                        <div
                            class="w-full h-full rounded-full bg-gradient-to-br from-sky-500 to-sky-700 flex items-center justify-center overflow-hidden transition-all duration-300 {{ $user->activeFrame ? 'scale-[1.02]' : 'border-4 border-white shadow-2xl' }}">
                            @if ($user->avatar)
                                <img src="{{ $user->avatar }}" alt="{{ $user->name }}"
                                    class="w-full h-full object-cover">
                            @else
                                <span
                                    class="text-4xl md:text-5xl font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            @endif
                        </div>
                        {{-- Frame overlay --}}
                        @if ($user->activeFrame)
                            <img src="{{ Storage::url($user->activeFrame->image_path) }}" alt=""
                                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[126%] h-[126%] max-w-none object-contain pointer-events-none drop-shadow-lg z-10 transition-all duration-300">
                        @endif
                    </div>

                    {{-- Info & Stats --}}
                    <div
                        class="flex-1 text-center md:text-left bg-white/70 backdrop-blur-xl p-5 md:p-6 rounded-[2rem] border border-white/60 shadow-[0_8px_30px_rgb(0,0,0,0.06)] w-full">
                        <div class="flex flex-col md:flex-row md:items-start gap-4 justify-between">
                            <div>
                                <div class="flex items-center justify-center md:justify-start gap-3 flex-wrap">
                                    <h1 class="text-3xl font-display font-bold text-gray-900">{{ $user->name }}</h1>
                                    {{-- Active Title Badge --}}
                                    @if ($user->activeTitle)
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border shadow-sm"
                                            style="color: {{ $user->activeTitle->color_hex }}; border-color: {{ $user->activeTitle->color_hex }}40; background-color: {{ $user->activeTitle->color_hex }}15;">
                                            {{ $user->activeTitle->name }}
                                        </span>
                                    @endif
                                </div>
                                @php
                                    $months = [
                                        '01' => 'Tháng 1',
                                        '02' => 'Tháng 2',
                                        '03' => 'Tháng 3',
                                        '04' => 'Tháng 4',
                                        '05' => 'Tháng 5',
                                        '06' => 'Tháng 6',
                                        '07' => 'Tháng 7',
                                        '08' => 'Tháng 8',
                                        '09' => 'Tháng 9',
                                        '10' => 'Tháng 10',
                                        '11' => 'Tháng 11',
                                        '12' => 'Tháng 12',
                                    ];
                                @endphp
                                <p class="text-gray-500 text-sm mt-1">
                                    <span class="font-medium text-gray-600">{{ $user->pronouns ?? 'Chưa rõ' }}</span>
                                    <span class="mx-1.5 opacity-50">•</span>
                                    Thành viên từ {{ $months[$user->created_at->format('m')] }},
                                    {{ $user->created_at->format('Y') }}
                                </p>

                                {{-- Movie Quote --}}
                                @if ($user->movie_quote)
                                    <div class="mt-3 max-w-lg pl-4 border-l-2 border-sky-500/40">
                                        <p class="text-gray-600 italic text-sm leading-relaxed">
                                            <svg class="inline-block w-3.5 h-3.5 text-sky-400/70 mr-0.5 -mt-1"
                                                fill="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10H14.017zM0 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151C7.546 6.068 5.983 8.789 5.983 11h4v10H0z" />
                                            </svg>
                                            {{ $user->movie_quote }}
                                        </p>
                                    </div>
                                @else
                                    <div class="mt-3 max-w-lg pl-4 border-l-2 border-gray-200">
                                        <p class="text-gray-400 italic text-sm leading-relaxed">
                                            Người dùng chưa có châm ngôn yêu thích.
                                        </p>
                                    </div>
                                @endif
                            </div>

                            {{-- Interactive Buttons --}}
                            <div class="flex items-center justify-center md:justify-end gap-3 flex-wrap">
                                @if ($isOwnProfile)
                                    <a href="{{ route('profile.edit') }}"
                                        class="inline-flex items-center justify-center px-6 py-2.5 font-semibold text-sm rounded-xl transition-all shadow-sm bg-white text-gray-700 border border-gray-200 hover:bg-gray-50 hover:border-gray-300">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Chỉnh sửa hồ sơ
                                    </a>
                                @else
                                    {{-- Follow Toggle --}}
                                    <div x-data="{
                                        isFollowing: {{ $isFollowing ? 'true' : 'false' }},
                                        followersCount: {{ $stats['followers_count'] }},
                                        async toggleFollow() {
                                            @guest window.location.href = '{{ route('login') }}'; return; @endguest
                                            const res = await fetch('{{ route('follow.toggle') }}', {
                                                method: 'POST',
                                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                                body: JSON.stringify({ user_id: {{ $user->id }} })
                                            });
                                            const data = await res.json();
                                            if (data.success) {
                                                this.isFollowing = data.is_following;
                                                this.followersCount += this.isFollowing ? 1 : -1;
                                            } else if (data.message) {
                                                alert(data.message);
                                            }
                                        }
                                    }">
                                        <button @click="toggleFollow()"
                                            :class="isFollowing ?
                                                'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' :
                                                'bg-sky-600 text-white border border-transparent hover:bg-sky-700'"
                                            class="inline-flex items-center justify-center px-6 py-2.5 font-semibold text-sm rounded-xl transition-all shadow-sm">
                                            <span x-text="isFollowing ? 'Đang theo dõi' : 'Theo dõi'"></span>
                                        </button>
                                        {{-- Update global text silently from Alpine if needed --}}
                                        <span class="hidden"
                                            x-effect="document.getElementById('display_followers_count').innerText = followersCount"></span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Stats Grid --}}
                        <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 mt-3 relative z-10">
                            <div
                                class="bg-white rounded-2xl p-4 border border-gray-200/80 shadow-[0_4px_12px_-2px_rgba(0,0,0,0.06)] hover:shadow-[0_8px_20px_-4px_rgba(0,0,0,0.1)] transition-all">
                                <div class="flex items-center gap-2 mb-2">
                                    <div
                                        class="w-8 h-8 rounded-full bg-sky-50 text-sky-600 flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </div>
                                    <div
                                        class="text-[11px] font-bold text-gray-500 uppercase tracking-widest whitespace-nowrap">
                                        Bài đánh giá
                                    </div>
                                </div>
                                <div class="text-2xl font-display font-bold text-gray-900">
                                    {{ $stats['reviews_count'] }}</div>
                            </div>
                            <div
                                class="bg-white rounded-2xl p-4 border border-gray-200/80 shadow-[0_4px_12px_-2px_rgba(0,0,0,0.06)] hover:shadow-[0_8px_20px_-4px_rgba(0,0,0,0.1)] transition-all">
                                <div class="flex items-center gap-2 mb-2">
                                    <div
                                        class="w-8 h-8 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                    </div>
                                    <div
                                        class="text-[11px] font-bold text-gray-500 uppercase tracking-widest whitespace-nowrap">
                                        Yêu Thích
                                    </div>
                                </div>
                                <div class="text-2xl font-display font-bold text-gray-900">
                                    {{ $stats['favorites_count'] }}</div>
                            </div>
                            <div
                                class="bg-white rounded-2xl p-4 border border-gray-200/80 shadow-[0_4px_12px_-2px_rgba(0,0,0,0.06)] hover:shadow-[0_8px_20px_-4px_rgba(0,0,0,0.1)] transition-all">
                                <div class="flex items-center gap-2 mb-2">
                                    <div
                                        class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </div>
                                    <div
                                        class="text-[11px] font-bold text-gray-500 uppercase tracking-widest whitespace-nowrap">
                                        Theo dõi
                                        tôi</div>
                                </div>
                                <div class="text-2xl font-display font-bold text-gray-900" id="display_followers_count">
                                    {{ $stats['followers_count'] }}</div>
                            </div>
                            <div
                                class="bg-white rounded-2xl p-4 border border-gray-200/80 shadow-[0_4px_12px_-2px_rgba(0,0,0,0.06)] hover:shadow-[0_8px_20px_-4px_rgba(0,0,0,0.1)] transition-all">
                                <div class="flex items-center gap-2 mb-2">
                                    <div
                                        class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-500 flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                        </svg>
                                    </div>
                                    <div
                                        class="text-[11px] font-bold text-gray-500 uppercase tracking-widest whitespace-nowrap">
                                        Đang theo
                                        dõi</div>
                                </div>
                                <div class="text-2xl font-display font-bold text-gray-900">
                                    {{ $stats['following_count'] }}</div>
                            </div>
                            {{-- Reputation Score --}}
                            <div
                                class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl p-4 border border-amber-200/60 shadow-[0_4px_12px_-2px_rgba(245,158,11,0.15)] hover:shadow-[0_8px_20px_-4px_rgba(245,158,11,0.25)] transition-all relative overflow-hidden">
                                <div class="absolute -top-4 -right-4 p-2 opacity-[0.08] pointer-events-none">
                                    <svg class="w-24 h-24 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="relative z-10">
                                    <div class="flex items-center gap-2 mb-2">
                                        <div
                                            class="w-8 h-8 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 text-white flex items-center justify-center shadow-lg shadow-orange-500/30">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div
                                            class="text-[11px] font-bold text-amber-700 uppercase tracking-widest whitespace-nowrap">
                                            Uy tín</div>
                                    </div>
                                    <div class="text-2xl font-display font-bold text-amber-600 drop-shadow-sm">
                                        {{ number_format($user->reputation_score) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Giới Thiệu Bản Thân (Dedicated Section) --}}
            <div class="mb-8 -mt-2">
                <div
                    class="bg-white/80 backdrop-blur-sm rounded-3xl border border-gray-100/50 shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-6 md:p-8 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-[0.03] pointer-events-none">
                        <svg class="w-32 h-32 text-indigo-600" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10H14.017zM0 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151C7.546 6.068 5.983 8.789 5.983 11h4v10H0z" />
                        </svg>
                    </div>
                    <div class="flex items-center gap-3 mb-4 relative z-10">
                        <div
                            class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-50 to-indigo-100 text-indigo-600 flex items-center justify-center shadow-inner border border-indigo-200/50">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-display font-bold text-gray-900">Giới thiệu bản thân</h2>
                    </div>
                    <div class="relative z-10 text-gray-700 leading-relaxed text-base">
                        @if ($user->bio)
                            {!! nl2br(e($user->bio)) !!}
                        @else
                            <span class="italic text-gray-400">Đang chuẩn bị kịch bản cho phần giới thiệu bản
                                thân...</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Top 4 Phim Tâm Đắc --}}
            @if ($user->topMovies->isNotEmpty())
                <div class="mb-8">
                    <div class="flex items-center gap-3 mb-5">
                        <div
                            class="w-9 h-9 rounded-xl bg-gradient-to-br from-rose-500 to-pink-600 flex items-center justify-center shadow-lg shadow-rose-500/20">
                            <svg class="w-4.5 h-4.5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-display font-bold text-gray-900">Top Phim Tâm Đắc</h2>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach ($user->topMovies as $index => $movie)
                            <a href="{{ route('movies.show', $movie) }}"
                                class="group relative aspect-[2/3] rounded-2xl overflow-hidden border border-gray-200/50 shadow-lg block hover:shadow-xl transition-shadow duration-300">
                                <img src="{{ $movie->poster }}" alt="{{ $movie->title }}"
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                                    loading="lazy">
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-gray-900/95 via-gray-900/20 to-transparent">
                                </div>
                                <div class="absolute bottom-0 left-0 right-0 p-4">
                                    <p class="text-white font-bold text-sm line-clamp-2 drop-shadow-lg">
                                        {{ $movie->title }}</p>
                                    @if ($movie->release_date)
                                        <p class="text-gray-300 text-xs mt-1">
                                            {{ \Carbon\Carbon::parse($movie->release_date)->format('Y') }}</p>
                                    @endif
                                </div>
                                <div
                                    class="absolute top-3 left-3 w-8 h-8 bg-white/90 backdrop-blur-sm text-gray-900 text-xs font-bold rounded-xl flex items-center justify-center border border-white/50 shadow-md">
                                    {{ $index + 1 }}
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Body Content: Favorites & Recent Reviews --}}
            <div class="grid lg:grid-cols-3 gap-8">

                {{-- Left sidebar: Favorites & Bio --}}
                <div class="lg:col-span-1 space-y-6">
                    <div
                        class="bg-white/80 backdrop-blur-sm rounded-2xl border border-gray-100/50 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.04)] p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2.5">
                                <div
                                    class="w-8 h-8 rounded-lg bg-rose-50 text-rose-500 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-base font-bold text-gray-900">Yêu thích gần đây</h3>
                            </div>
                            <a href="{{ route('profile.favorites', $user) }}"
                                class="inline-flex items-center gap-1 text-xs font-semibold text-sky-600 hover:text-sky-700 transition-colors">
                                Xem tất cả
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                        @if ($user->favorites->isEmpty())
                            <div
                                class="text-center py-10 bg-gray-50/50 rounded-xl border border-dashed border-gray-200">
                                <div
                                    class="w-12 h-12 bg-white rounded-full flex items-center justify-center mx-auto mb-3 shadow-sm border border-gray-100">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                </div>
                                <p class="text-gray-500 text-sm">Chưa có phim yêu thích.</p>
                            </div>
                        @else
                            <div class="grid grid-cols-3 gap-2.5">
                                @foreach ($user->favorites as $favMovie)
                                    <a href="{{ route('movies.show', $favMovie) }}"
                                        class="block group relative rounded-xl overflow-hidden aspect-[2/3] border border-gray-100/50 shadow-sm hover:shadow-md transition-shadow">
                                        <img src="{{ $favMovie->poster }}" alt="{{ $favMovie->title }}"
                                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                                            loading="lazy">
                                        <div
                                            class="absolute inset-0 bg-gradient-to-t from-gray-900/90 via-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-2">
                                            <span
                                                class="text-[10px] font-bold text-white leading-tight line-clamp-2 drop-shadow">{{ $favMovie->title }}</span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>

                </div>

                {{-- Right Column: Recent Reviews --}}
                <div class="lg:col-span-2 space-y-5">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-9 h-9 rounded-xl bg-gradient-to-br from-sky-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-sky-500/20">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-display font-bold text-gray-900">Đánh giá gần đây</h2>
                    </div>

                    @forelse($user->reviews as $review)
                        <div
                            class="bg-white/80 backdrop-blur-sm rounded-2xl border border-gray-100/50 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.04)] hover:shadow-[0_8px_30px_-6px_rgba(0,0,0,0.08)] transition-all duration-300 p-5 group flex flex-col md:flex-row gap-5">
                            @if ($review->movie)
                                <a href="{{ route('movies.show', $review->movie) }}"
                                    class="shrink-0 w-24 md:w-28 aspect-[2/3] rounded-xl overflow-hidden shadow-md border border-gray-100/50 block">
                                    <img src="{{ $review->movie->poster }}" alt="{{ $review->movie->title }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                        loading="lazy">
                                </a>
                            @endif
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-4 mb-2">
                                    <div>
                                        <h3 class="font-bold text-gray-900 text-base line-clamp-1 mb-1">
                                            <a href="{{ route('movies.show', $review->movie) }}"
                                                class="hover:text-sky-600 transition-colors">{{ $review->title ?? 'Đánh giá phim ' . $review->movie->title }}</a>
                                        </h3>
                                        @if ($review->rating)
                                            <x-star-rating :rating="$review->rating" />
                                        @endif
                                    </div>
                                    <span
                                        class="inline-flex items-center gap-1 text-[11px] text-gray-400 whitespace-nowrap bg-gray-50 px-2.5 py-1 rounded-full">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $review->created_at->diffForHumans() }}
                                    </span>
                                </div>

                                @if ($review->content)
                                    @if ($review->is_spoiler)
                                        <x-spoiler-toggle>
                                            <div class="text-gray-600 text-sm leading-relaxed mb-4 line-clamp-3">
                                                {{ $review->content }}
                                            </div>
                                        </x-spoiler-toggle>
                                    @else
                                        <p class="text-gray-600 text-sm leading-relaxed mb-4 line-clamp-3">
                                            {{ $review->content }}
                                        </p>
                                    @endif
                                @endif

                                <div class="flex items-center gap-5 border-t border-gray-100/80 pt-3 mt-auto">
                                    <span
                                        class="flex items-center gap-1.5 text-xs font-medium text-gray-400 hover:text-rose-500 transition-colors cursor-default">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                        {{ $review->likes_count }} lượt thích
                                    </span>
                                    <span
                                        class="flex items-center gap-1.5 text-xs font-medium text-gray-400 hover:text-sky-500 transition-colors cursor-default">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                        {{ $review->comments_count }} bình luận
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl p-12 text-center border-dashed border-2 border-gray-200 bg-gray-50/50">
                            <div
                                class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100 shadow-sm relative">
                                <div class="absolute inset-0 bg-sky-400/20 rounded-full blur-md"></div>
                                <svg class="w-8 h-8 text-sky-500 relative z-10" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-1">Chưa có bài đánh giá nào</h3>
                            <p class="text-gray-500 text-sm">Người dùng này chưa có hoạt động chia sẻ cảm nhận phim.
                            </p>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
