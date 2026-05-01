<x-app-layout>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        .wl {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            min-height: 100vh;
        }

        /* ─── HEADER ─────────────────────────────────────── */
        .wl-header {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 1.75rem 0 0;
        }

        .wl-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        .wl-title-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 1.5rem;
        }

        .wl-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #10b981, #0d9488);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.28);
            flex-shrink: 0;
        }

        .wl-h1 {
            font-size: 1.3rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.025em;
        }

        .wl-sub {
            font-size: 0.75rem;
            color: #94a3b8;
            margin-top: 1px;
        }

        /* ─── TABS ──────────────────────────────────────── */
        .wl-tabs {
            display: inline-flex;
            border: 1px solid #e2e8f0;
            border-radius: 8px 8px 0 0;
            overflow: hidden;
            background: #fff;
            position: relative;
            bottom: -1px;
        }

        .wl-tab {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 16px;
            font-size: 0.8rem;
            font-weight: 500;
            color: #94a3b8;
            background: #fff;
            cursor: pointer;
            white-space: nowrap;
            border-right: 1px solid #e2e8f0;
            transition: background 0.14s, color 0.14s;
            border-bottom: 2px solid transparent;
        }

        .wl-tab:last-child {
            border-right: none;
        }

        .wl-tab:hover {
            background: #fafafa;
            color: #475569;
        }

        .wl-tab.is-active {
            color: #6366f1;
            font-weight: 600;
            background: #fafbff;
            border-bottom-color: #6366f1;
        }

        .wl-badge {
            font-size: 0.65rem;
            font-weight: 700;
            padding: 1px 6px;
            border-radius: 4px;
            background: #f1f5f9;
            color: #94a3b8;
        }

        .wl-tab.is-active .wl-badge {
            background: #ede9fe;
            color: #7c3aed;
        }

        /* ─── BODY ───────────────────────────────────────── */
        .wl-body {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1.75rem 1.5rem 3rem;
        }

        .wl-panel {
            display: none;
        }

        .wl-panel.is-active {
            display: block;
            animation: fadeIn .18s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0
            }

            to {
                opacity: 1
            }
        }

        /* ─── GRID ───────────────────────────────────────── */
        .wl-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
        }

        @media (min-width: 600px) {
            .wl-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media (min-width: 900px) {
            .wl-grid {
                grid-template-columns: repeat(5, 1fr);
                gap: 16px;
            }
        }

        @media (min-width: 1100px) {
            .wl-grid {
                grid-template-columns: repeat(6, 1fr);
            }
        }

        /* ─── CARD ───────────────────────────────────────── */
        .wl-card {
            border: 1px solid #c7d2e7;
            border-radius: 10px;
            background: #fff;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: box-shadow .2s, border-color .2s;
        }

        .wl-card:hover {
            border-color: #a5b4fc;
            box-shadow: 0 4px 18px rgba(99, 102, 241, 0.12);
        }

        /* Poster */
        .wl-poster {
            display: block;
            aspect-ratio: 2/3;
            overflow: hidden;
            background: #f1f5f9;
            position: relative;
            flex-shrink: 0;
        }

        .wl-poster img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .wl-no-poster {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
            padding: .75rem;
        }

        /* Rating badge on poster */
        .wl-r {
            position: absolute;
            bottom: 7px;
            left: 7px;
            background: rgba(0, 0, 0, 0.62);
            backdrop-filter: blur(6px);
            border-radius: 5px;
            padding: 2px 6px;
            font-size: 0.6rem;
            font-weight: 700;
            color: #fbbf24;
            display: flex;
            align-items: center;
            gap: 2px;
            pointer-events: none;
        }

        /* Info block */
        .wl-info {
            padding: 9px 10px 7px;
            border-top: 1px solid #f1f5f9;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        /* Title: always 2-line height so cards align */
        .wl-title {
            font-size: 0.74rem;
            font-weight: 600;
            color: #1e293b;
            line-height: 1.35;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            /* Fixed 2-line height regardless of content */
            min-height: calc(1.35em * 2);
        }

        .wl-year {
            font-size: 0.67rem;
            color: #94a3b8;
            font-weight: 500;
        }

        /* ─── ACTION BAR ─────────────────────────────────── */
        .wl-actions {
            display: flex;
            align-items: stretch;
            border-top: 1px solid #cbd5e1;
            flex-shrink: 0;
        }

        /* Status button */
        .wl-status-btn {
            flex: 1;
            min-width: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 4px;
            padding: 7px 9px;
            font-size: 0.7rem;
            font-weight: 600;
            color: #334155;
            background: transparent;
            border: none;
            cursor: pointer;
            transition: background .14s, color .14s;
            text-align: left;
        }

        .wl-status-btn:hover {
            background: #fafbff;
            color: #6366f1;
        }

        .wl-status-btn .wl-chevron {
            flex-shrink: 0;
            color: #cbd5e1;
            transition: transform .18s;
        }

        .wl-status-open .wl-chevron {
            transform: rotate(180deg);
        }

        /* Dropdown */
        .wl-dropdown {
            position: absolute;
            bottom: calc(100% + 6px);
            left: 0;
            min-width: 148px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            box-shadow: 0 8px 28px rgba(15, 23, 42, .11), 0 2px 8px rgba(15, 23, 42, .06);
            z-index: 60;
            padding: 4px;
        }

        .wl-drop-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            width: 100%;
            padding: 7px 10px;
            font-size: 0.74rem;
            font-weight: 500;
            color: #475569;
            background: transparent;
            border: none;
            border-radius: 7px;
            cursor: pointer;
            text-align: left;
            transition: background .12s, color .12s;
        }

        .wl-drop-item:hover {
            background: #f5f3ff;
            color: #6366f1;
        }

        .wl-drop-current {
            color: #6366f1;
            font-weight: 600;
            background: #f5f3ff;
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding: 7px 10px;
            width: 100%;
            font-size: 0.74rem;
            border-radius: 7px;
        }

        /* Separator */
        .wl-sep {
            width: 1px;
            background: #cbd5e1;
            flex-shrink: 0;
        }

        /* Delete button */
        .wl-del {
            width: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            background: transparent;
            border: none;
            cursor: pointer;
            transition: color .14s, background .14s;
            flex-shrink: 0;
            border-radius: 0 0 9px 0;
        }

        .wl-del:hover {
            color: #ef4444;
            background: #fef2f2;
        }

        /* ─── EMPTY ──────────────────────────────────────── */
        .wl-empty {
            border: 2px dashed #e2e8f0;
            background: #fff;
            border-radius: 14px;
            padding: 3rem 2rem;
            text-align: center;
        }

        .wl-empty-ico {
            width: 52px;
            height: 52px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }

        .wl-empty h3 {
            font-size: .9rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .wl-empty p {
            font-size: .78rem;
            color: #94a3b8;
            line-height: 1.6;
        }

        .wl-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #0f172a;
            color: #fff;
            font-weight: 600;
            font-size: .82rem;
            padding: 9px 20px;
            border-radius: 8px;
            margin-top: 1.4rem;
            transition: background .18s;
        }

        .wl-btn:hover {
            background: #1e293b;
        }
    </style>

    <div class="wl">

        {{-- HEADER --}}
        <div class="wl-header">
            <div class="wl-inner">
                <div class="wl-title-row">
                    <div class="wl-icon">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="wl-h1">Danh sách của tôi</h1>
                        <p class="wl-sub">Quản lý hành trình điện ảnh cá nhân</p>
                    </div>
                </div>

                @php
                    $tabs = [
                        'want_to_watch' => 'Muốn xem',
                        'watching' => 'Đang xem',
                        'watched' => 'Đã xem',
                        'dropped' => 'Bỏ dở',
                    ];
                @endphp

                <div class="wl-tabs">
                    @foreach ($tabs as $key => $label)
                        <button class="wl-tab {{ $loop->first ? 'is-active' : '' }}" data-tab="{{ $key }}"
                            onclick="wlGo('{{ $key }}')">
                            {{ $label }}
                            <span class="wl-badge">{{ $watchlists->get($key)?->count() ?? 0 }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- BODY --}}
        <div class="wl-body">

            @if ($watchlists->isEmpty())
                <div class="wl-empty">
                    <div class="wl-empty-ico">
                        <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                        </svg>
                    </div>
                    <h3>Danh sách trống</h3>
                    <p>Bạn chưa lưu bộ phim nào. Hãy khám phá và thêm phim yêu thích!</p>
                    <a href="{{ route('explore') }}" class="wl-btn">
                        Khám phá phim
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            @else
                @foreach ($tabs as $statusKey => $label)
                    <div id="wl-{{ $statusKey }}" class="wl-panel {{ $loop->first ? 'is-active' : '' }}">
                        @php $items = $watchlists->get($statusKey); @endphp

                        @if ($items && $items->isNotEmpty())
                            <div class="wl-grid">
                                @foreach ($items as $item)
                                    @php $movie = $item->movie; @endphp

                                    <div class="wl-card">
                                        {{-- Poster --}}
                                        <a href="{{ route('movies.show', $movie) }}" class="wl-poster">
                                            @if ($movie->poster)
                                                <img src="{{ $movie->poster }}" alt="{{ $movie->title }}"
                                                    loading="lazy">
                                            @else
                                                <div class="wl-no-poster">
                                                    <svg class="w-8 h-8 text-slate-300" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="1.5"
                                                            d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                                                    </svg>
                                                </div>
                                            @endif
                                            @if ($movie->avg_rating > 0)
                                                <div class="wl-r">
                                                    <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                    {{ number_format($movie->avg_rating, 1) }}
                                                </div>
                                            @endif
                                        </a>

                                        {{-- Info --}}
                                        <div class="wl-info">
                                            <p class="wl-title">{{ $movie->title }}</p>
                                            @if ($movie->release_date)
                                                <p class="wl-year">{{ $movie->release_date->format('Y') }}</p>
                                            @else
                                                <p class="wl-year">&nbsp;</p>
                                            @endif
                                        </div>

                                        {{-- Actions --}}
                                        <div class="wl-actions">
                                            {{-- Status dropdown (Alpine) --}}
                                            <div class="relative flex-1 min-w-0" x-data="{ open: false }"
                                                @click.outside="open = false">

                                                <button type="button" class="wl-status-btn"
                                                    :class="open ? 'wl-status-open' : ''" @click="open = !open">
                                                    <span class="truncate">{{ $label }}</span>
                                                    <svg class="wl-chevron" style="width:10px;height:10px"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </button>

                                                <div class="wl-dropdown" x-show="open"
                                                    x-transition:enter="transition ease-out duration-100"
                                                    x-transition:enter-start="opacity-0 scale-95"
                                                    x-transition:enter-end="opacity-100 scale-100"
                                                    x-transition:leave="transition ease-in duration-75"
                                                    x-transition:leave-start="opacity-100 scale-100"
                                                    x-transition:leave-end="opacity-0 scale-95" style="display:none;">
                                                    @foreach ($tabs as $optKey => $optLabel)
                                                        @if ($optKey === $statusKey)
                                                            <div class="wl-drop-current">
                                                                {{ $optLabel }}
                                                                <svg style="width:12px;height:12px;color:#6366f1;flex-shrink:0"
                                                                    fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                                </svg>
                                                            </div>
                                                        @else
                                                            <form action="{{ route('watchlist.toggle') }}"
                                                                method="POST">
                                                                @csrf
                                                                <input type="hidden" name="movie_id"
                                                                    value="{{ $item->movie_id }}">
                                                                <input type="hidden" name="status"
                                                                    value="{{ $optKey }}">
                                                                <button type="submit"
                                                                    class="wl-drop-item">{{ $optLabel }}</button>
                                                            </form>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>

                                            <div class="wl-sep"></div>

                                            {{-- Delete --}}
                                            <form action="{{ route('watchlist.toggle') }}" method="POST"
                                                style="display:contents;">
                                                @csrf
                                                <input type="hidden" name="movie_id" value="{{ $item->movie_id }}">
                                                <button type="submit" class="wl-del" title="Xóa khỏi danh sách"
                                                    onclick="return confirm('Xóa phim này khỏi danh sách?')">
                                                    <svg style="width:13px;height:13px" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="wl-empty">
                                <div class="wl-empty-ico">
                                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                                    </svg>
                                </div>
                                <h3>Chưa có phim nào</h3>
                                <p>Thêm phim vào mục <strong>{{ $label }}</strong> để xem ở đây.</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif

        </div>
    </div>

    <script>
        function wlGo(key) {
            document.querySelectorAll('.wl-tab').forEach(t =>
                t.classList.toggle('is-active', t.dataset.tab === key));
            document.querySelectorAll('.wl-panel').forEach(p =>
                p.classList.toggle('is-active', p.id === 'wl-' + key));
        }
    </script>
</x-app-layout>
