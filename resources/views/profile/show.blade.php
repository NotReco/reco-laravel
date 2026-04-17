<x-app-layout>

    {{-- =====================================================================
     PROFILE PAGE — RecoDB Minimalist Redesign
     Clean minimalist grid, narrow cover banner, monochrome icons
     ===================================================================== --}}

    <style>
        /* ── Google Font ── */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        .profile-root {
            font-family: 'Inter', sans-serif;
        }

        /* ── Cover Banner ── */
        .cover-wrap {
            width: 100%;
            background: #f1f5f9;
            position: relative;
            overflow: hidden;
        }

        .cover-wrap img.cover-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: center;
            display: block;
        }

        /* ── Section card (Minimalist) ── */
        .section-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.03), 0 0 3px rgba(0, 0, 0, 0.01);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .section-card:hover {
            box-shadow: 0 8px 30px -4px rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 1.25rem;
        }

        .section-icon {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: #f8fafc;
            color: #64748b;
            border: 1px solid #e2e8f0;
        }

        .section-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #1e293b;
        }


        /* ── Top movies grid ── */
        .top-movie-card {
            position: relative;
            aspect-ratio: 2/3;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            display: block;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .top-movie-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .top-movie-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .movie-gradient {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(15, 23, 42, 0.85) 0%, rgba(15, 23, 42, 0.1) 40%, transparent 60%);
        }

        .movie-rank {
            position: absolute;
            top: 8px;
            left: 8px;
            width: 22px;
            height: 22px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 700;
            color: #334155;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .movie-info {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 8px;
        }

        /* ── Review card ── */
        .review-card {
            background: #fff;
            border: 1px solid #f1f5f9;
            border-radius: 10px;
            padding: 1rem;
            display: flex;
            gap: 1rem;
            transition: border-color 0.2s;
        }

        .review-card:hover {
            border-color: #e2e8f0;
        }

        /* ── Empty state (Enhanced) ── */
        .empty-state {
            border: 2px dashed #e2e8f0;
            background: #f8fafc;
            border-radius: 16px;
            padding: 3rem 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .empty-state:hover {
            border-color: #cbd5e1;
            background: #f1f5f9;
        }

        .empty-icon-wrap {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            background: #ffffff;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), inset 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .empty-icon {
            width: 24px;
            height: 24px;
            color: #64748b;
        }


        /* ── Buttons ── */
        .btn-minimal {
            background: #fff;
            border: 1px solid #e2e8f0;
            color: #475569;
            transition: all 0.2s;
        }

        .btn-minimal:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #1e293b;
        }

        .btn-primary-minimal {
            background: #0f172a;
            color: #fff;
            border: 1px solid transparent;
            transition: background 0.2s;
        }

        .btn-primary-minimal:hover {
            background: #1e293b;
        }
    </style>

    <div class="profile-root py-8 min-h-screen bg-[#f8fafc]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- ════════════════════════════════════════════════════════
             PROFILE HEADER CARD
             ════════════════════════════════════════════════════════ --}}
            <div class="section-card mb-6 overflow-visible">

                {{-- ── Cover Banner (Narrow) ── --}}
                @if ($user->cover_photo)
                    <div class="relative cover-wrap rounded-t-[12px] h-40 md:h-64 overflow-hidden bg-slate-800">
                        {{-- Ambient background --}}
                        <img class="absolute inset-0 w-full h-full object-cover blur-[20px] scale-110 opacity-70"
                            src="{{ Str::startsWith($user->cover_photo, 'http') ? $user->cover_photo : asset($user->cover_photo) }}"
                            alt="" aria-hidden="true">
                        {{-- Actual uncropped cover --}}
                        <img class="relative z-10 w-full h-full object-contain filter drop-shadow-[0_10px_10px_rgba(0,0,0,0.5)]"
                            src="{{ Str::startsWith($user->cover_photo, 'http') ? $user->cover_photo : asset($user->cover_photo) }}"
                            alt="Cover Photo">
                    </div>
                @else
                    <div
                        class="rounded-t-[12px] h-32 md:h-48 bg-gradient-to-r from-slate-100 to-slate-200 flex items-center justify-center">
                        <svg class="text-slate-300 w-12 h-12" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M18 3v2h-2V3H8v2H6V3H4v18h2v-2h2v2h8v-2h2v2h2V3h-2zM8 17H6v-2h2v2zm0-4H6v-2h2v2zm0-4H6V7h2v2zm10 8h-2v-2h2v2zm0-4h-2v-2h2v2zm0-4h-2V7h2v2z" />
                        </svg>
                    </div>
                @endif

                {{-- ── Avatar + Info ── --}}
                <div class="px-5 md:px-8 pb-7 pt-9">
                    <div class="flex flex-col md:flex-row items-center md:items-start gap-6 md:gap-10 ml-[2px]">

                        {{-- Avatar with Frame (PRESERVED EXACTLY) --}}
                        <div class="relative shrink-0" style="width: 108px; height: 108px;">
                            <div
                                class="w-full h-full rounded-full bg-gradient-to-br from-sky-500 to-sky-700 flex items-center justify-center overflow-hidden transition-all duration-300 {{ $user->activeFrame ? 'scale-[1.0475]' : 'border-4 border-white shadow-sm' }}">
                                @if ($user->avatar)
                                    <img src="{{ $user->avatar }}" alt="{{ $user->name }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <span
                                        class="text-3xl font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                @endif
                            </div>
                            {{-- Frame overlay --}}
                            @if ($user->activeFrame)
                                <img src="{{ Storage::url($user->activeFrame->image_path) }}" alt=""
                                    class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 max-w-none object-contain pointer-events-none drop-shadow z-10 transition-all duration-300"
                                    style="width: 126%; height: 126%;">
                            @endif
                        </div>

                        {{-- User Info Block --}}
                        <div class="flex-1 flex flex-col md:flex-row items-center md:items-start justify-between gap-5">
                            <div class="text-center md:text-left w-full md:w-auto">
                                <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 mb-1">
                                    <h1 class="text-[1.6rem] font-bold text-slate-900 leading-tight">{{ $user->name }}
                                    </h1>
                                    @if ($user->activeTitle)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] font-semibold tracking-wide border"
                                            style="color: {{ $user->activeTitle->color_hex }}; border-color: {{ $user->activeTitle->color_hex }}40; background-color: {{ $user->activeTitle->color_hex }}10;">
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
                                <p
                                    class="text-[13px] text-slate-500 flex items-center justify-center md:justify-start gap-2">
                                    @if ($user->pronouns)
                                        <span
                                            class="font-medium text-slate-600 bg-slate-100 px-2 py-0.5 rounded">{{ $user->pronouns }}</span>
                                        <span class="text-slate-300">•</span>
                                    @endif
                                    <span>Thành viên từ {{ $months[$user->created_at->format('m')] }},
                                        {{ $user->created_at->format('Y') }}</span>
                                </p>

                                @if ($user->movie_quote)
                                    <div
                                        class="mt-3.5 bg-slate-50/70 border border-slate-100 rounded-xl p-3 max-w-lg inline-block text-left relative overflow-hidden">
                                        <div class="absolute top-0 left-0 w-1 h-full bg-slate-300"></div>
                                        <p
                                            class="text-slate-600 italic text-[13px] font-medium leading-relaxed pl-2 relative z-10 w-full break-words">
                                            "{{ $user->movie_quote }}"
                                        </p>
                                    </div>
                                @endif
                            </div>

                            {{-- Actions --}}
                            <div class="shrink-0 flex justify-center w-full md:w-auto mt-2 md:mt-1.5">
                                @if ($isOwnProfile)
                                    <a href="{{ route('profile.edit') }}"
                                        class="btn-minimal inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold rounded-lg shadow-sm h-fit">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                        Chỉnh sửa hồ sơ
                                    </a>
                                @else
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
                                            } else if (data.message) { alert(data.message); }
                                        }
                                    }">
                                        <button @click="toggleFollow()"
                                            class="inline-flex items-center gap-1.5 px-5 py-2 text-xs font-semibold rounded-lg shadow-sm transition-all h-fit"
                                            :class="isFollowing ? 'btn-minimal' : 'btn-primary-minimal'">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    x-show="!isFollowing" d="M12 4v16m8-8H4" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    x-show="isFollowing" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span x-text="isFollowing ? 'Đang theo dõi' : 'Theo dõi'"></span>
                                        </button>
                                        <span class="hidden"
                                            x-effect="document.getElementById('display_followers_count').innerText = followersCount"></span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- ── Colorful Stats Grid ── --}}
                    <div class="mt-7">
                        <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 md:gap-4">
                            {{-- Đánh giá --}}
                            <div
                                class="flex items-center gap-3 bg-gradient-to-br from-indigo-500 to-violet-600 hover:from-indigo-600 hover:to-violet-700 p-3 rounded-xl border border-indigo-400/30 transition-all shadow-md shadow-indigo-500/20 text-white">
                                <div
                                    class="w-[38px] h-[38px] rounded-lg bg-white/20 border border-white/30 backdrop-blur-sm flex items-center justify-center shadow-inner shrink-0">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <span
                                        class="text-xl font-extrabold leading-none mb-1">{{ $stats['reviews_count'] }}</span>
                                    <span
                                        class="text-[10px] font-bold text-indigo-100 uppercase tracking-wider truncate">Đánh
                                        giá</span>
                                </div>
                            </div>

                            {{-- Yêu thích --}}
                            <div
                                class="flex items-center gap-3 bg-gradient-to-br from-pink-500 to-rose-600 hover:from-pink-600 hover:to-rose-700 p-3 rounded-xl border border-pink-400/30 transition-all shadow-md shadow-pink-500/20 text-white">
                                <div
                                    class="w-[38px] h-[38px] rounded-lg bg-white/20 border border-white/30 backdrop-blur-sm flex items-center justify-center shadow-inner shrink-0">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <span
                                        class="text-xl font-extrabold leading-none mb-1">{{ $stats['favorites_count'] }}</span>
                                    <span
                                        class="text-[10px] font-bold text-pink-100 uppercase tracking-wider truncate">Yêu
                                        thích</span>
                                </div>
                            </div>

                            {{-- Người theo dõi --}}
                            <div
                                class="flex items-center gap-3 bg-gradient-to-br from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 p-3 rounded-xl border border-sky-400/30 transition-all shadow-md shadow-sky-500/20 text-white">
                                <div
                                    class="w-[38px] h-[38px] rounded-lg bg-white/20 border border-white/30 backdrop-blur-sm flex items-center justify-center shadow-inner shrink-0">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                                    </svg>
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <span class="text-xl font-extrabold leading-none mb-1"
                                        id="display_followers_count">{{ $stats['followers_count'] }}</span>
                                    <span
                                        class="text-[10px] font-bold text-sky-100 uppercase tracking-wider truncate">Người
                                        theo dõi</span>
                                </div>
                            </div>

                            {{-- Đang theo dõi --}}
                            <div
                                class="flex items-center gap-3 bg-gradient-to-br from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 p-3 rounded-xl border border-emerald-400/30 transition-all shadow-md shadow-emerald-500/20 text-white">
                                <div
                                    class="w-[38px] h-[38px] rounded-lg bg-white/20 border border-white/30 backdrop-blur-sm flex items-center justify-center shadow-inner shrink-0">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                        <path fill-rule="evenodd"
                                            d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <span
                                        class="text-xl font-extrabold leading-none mb-1">{{ $stats['following_count'] }}</span>
                                    <span
                                        class="text-[10px] font-bold text-emerald-100 uppercase tracking-wider truncate">Đang
                                        theo dõi</span>
                                </div>
                            </div>

                            {{-- Uy Tín --}}
                            <div
                                class="flex items-center gap-3 bg-gradient-to-br from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 p-3 rounded-xl border border-amber-400/30 transition-all shadow-md shadow-amber-500/20 text-white col-span-2 lg:col-span-1">
                                <div
                                    class="w-[38px] h-[38px] rounded-lg bg-white/20 border border-white/30 backdrop-blur-sm flex items-center justify-center shadow-inner shrink-0">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <span
                                        class="text-xl font-extrabold leading-none mb-1">{{ number_format($user->reputation_score) }}</span>
                                    <span
                                        class="text-[10px] font-extrabold text-amber-100 uppercase tracking-widest truncate">Uy
                                        tín</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ════════════════════════════════════════════════════════
             MAIN CONTENT — 2-Column Grid
             ════════════════════════════════════════════════════════ --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- ── RIGHT MAIN COLUMN (2/3 width) ── --}}
                <div class="lg:col-span-2 space-y-6 order-2">

                    {{-- Top Phim Tâm Đắc --}}
                    <div class="section-card p-6">
                        <div class="section-header">
                            <div
                                class="w-8 h-8 rounded-lg bg-gradient-to-br from-amber-400 to-orange-500 shadow-sm flex items-center justify-center shrink-0">
                                <svg width="16" height="16" fill="currentColor" class="text-white"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                                </svg>
                            </div>
                            <h2 class="section-title">Top Phim Tâm Đắc</h2>
                        </div>

                        @if ($user->topMovies->isNotEmpty())
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach ($user->topMovies as $index => $movie)
                                    <a href="{{ route('movies.show', $movie) }}" class="top-movie-card">
                                        <img src="{{ $movie->poster }}" alt="{{ $movie->title }}" loading="lazy">
                                        <div class="movie-gradient"></div>
                                        <div class="movie-rank">{{ $index + 1 }}</div>
                                        <div class="movie-info">
                                            <p
                                                class="text-white font-semibold text-[11px] leading-tight line-clamp-2 drop-shadow-md">
                                                {{ $movie->title }}</p>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state">
                                <div class="empty-icon-wrap">
                                    <svg class="empty-icon" fill="none" stroke="currentColor" stroke-width="1.5"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                    </svg>
                                </div>
                                <h3 class="text-[13px] font-bold text-slate-700 mb-1">Chưa có Phim Tâm Đắc</h3>
                                <p class="text-slate-500 text-xs leading-relaxed">Thành viên này đang cân nhắc quá
                                    nhiều bộ phim tuyệt vời, chưa thể chọn ra bộ phim tâm đắc.</p>
                            </div>
                        @endif
                    </div>

                    {{-- Đánh giá gần đây --}}
                    <div class="section-card p-6">
                        <div class="section-header">
                            <div
                                class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 shadow-sm flex items-center justify-center shrink-0">
                                <svg width="16" height="16" fill="none" class="text-white"
                                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </div>
                            <h2 class="section-title">Đánh giá gần đây</h2>
                        </div>

                        <div class="space-y-4">
                            @forelse($user->reviews as $review)
                                <div class="review-card">
                                    @if ($review->movie)
                                        <a href="{{ route('movies.show', $review->movie) }}"
                                            class="shrink-0 block rounded-md overflow-hidden bg-slate-100 border border-slate-200"
                                            style="width: 54px; height: 81px;">
                                            <img src="{{ $review->movie->poster }}"
                                                alt="{{ $review->movie->title }}" class="w-full h-full object-cover"
                                                loading="lazy">
                                        </a>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-2 mb-1.5">
                                            <h3 class="font-semibold text-slate-800 text-[13px] line-clamp-1">
                                                <a href="{{ route('movies.show', $review->movie) }}"
                                                    class="hover:text-sky-600 transition-colors">
                                                    {{ $review->title ?? 'Đánh giá: ' . $review->movie->title }}
                                                </a>
                                            </h3>
                                            <span class="text-[10px] text-slate-400 font-medium">
                                                {{ $review->created_at->diffForHumans() }}
                                            </span>
                                        </div>

                                        @if ($review->rating)
                                            <div class="mb-2">
                                                <x-star-rating :rating="$review->rating" />
                                            </div>
                                        @endif

                                        @if ($review->content)
                                            @if ($review->is_spoiler)
                                                <x-spoiler-toggle>
                                                    <p class="text-slate-600 text-xs leading-relaxed line-clamp-2">
                                                        {{ $review->content }}</p>
                                                </x-spoiler-toggle>
                                            @else
                                                <p class="text-slate-600 text-xs leading-relaxed line-clamp-2">
                                                    {{ $review->content }}</p>
                                            @endif
                                        @endif

                                        <div class="flex items-center gap-4 mt-3">
                                            <span
                                                class="flex items-center gap-1 text-[11px] text-slate-400 font-medium">
                                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                                </svg>
                                                {{ $review->likes_count }}
                                            </span>
                                            <span
                                                class="flex items-center gap-1 text-[11px] text-slate-400 font-medium">
                                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                </svg>
                                                {{ $review->comments_count }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="empty-state">
                                    <div class="empty-icon-wrap">
                                        <svg class="empty-icon" fill="none" stroke="currentColor"
                                            stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 3l5 5" />
                                        </svg>
                                    </div>
                                    <h3 class="text-[13px] font-bold text-slate-700 mb-1">Chưa có bài đánh giá</h3>
                                    <p class="text-slate-500 text-xs leading-relaxed">Người dùng này chưa chia sẻ cảm
                                        nhận về bộ phim nào.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- ── LEFT SIDEBAR (1/3 width) ── --}}
                <div class="lg:col-span-1 space-y-6 order-1">

                    {{-- Giới thiệu bản thân --}}
                    <div class="section-card p-6">
                        <div class="section-header">
                            <div
                                class="w-8 h-8 rounded-lg bg-gradient-to-br from-sky-400 to-blue-600 shadow-sm flex items-center justify-center shrink-0">
                                <svg width="16" height="16" fill="none" stroke="currentColor"
                                    class="text-white" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <h2 class="section-title">Giới thiệu bản thân</h2>
                        </div>

                        <div class="text-slate-600 text-[13px] leading-relaxed">
                            @if ($user->bio)
                                {!! nl2br(e($user->bio)) !!}
                            @else
                                <div class="text-center py-2">
                                    <p class="text-slate-400 text-xs">Đang chuẩn bị kịch bản cho phần giới thiệu...</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Yêu thích gần đây --}}
                    <div class="section-card p-6">
                        <div class="section-header">
                            <div
                                class="w-8 h-8 rounded-lg bg-gradient-to-br from-pink-500 to-rose-600 shadow-sm flex items-center justify-center shrink-0">
                                <svg width="16" height="16" fill="none" class="text-white"
                                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </div>
                            <div class="flex-1 flex items-center justify-between">
                                <h2 class="section-title">Yêu thích gần đây</h2>
                                <a href="{{ route('profile.favorites', $user) }}"
                                    class="text-[11px] font-medium text-sky-500 hover:text-sky-600 hover:underline transition-colors shrink-0">
                                    Xem tất cả
                                </a>
                            </div>
                        </div>

                        @if ($user->favorites->isEmpty())
                            <div class="empty-state">
                                <div class="empty-icon-wrap">
                                    <svg class="empty-icon" fill="none" stroke="currentColor" stroke-width="1.5"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                </div>
                                <p class="text-slate-500 text-xs">Chưa có phim yêu thích nào.</p>
                            </div>
                        @else
                            <div class="grid grid-cols-3 gap-3">
                                @foreach ($user->favorites as $favMovie)
                                    <a href="{{ route('movies.show', $favMovie) }}"
                                        class="block relative rounded-md overflow-hidden border border-[e2e8f0] shadow-sm"
                                        style="aspect-ratio: 2/3;">
                                        <img src="{{ $favMovie->poster }}" alt="{{ $favMovie->title }}"
                                            class="w-full h-full object-cover transition-opacity duration-300 hover:opacity-80"
                                            loading="lazy">
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
