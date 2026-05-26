<x-admin-layout :title="'Tổng quan'" pageTitle="Tổng quan">

{{-- ── Stats Cards ───────────────────────────────────────────── --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-8">
    @php
        $cards = [
            ['label' => 'Tổng phim lẻ',        'value' => $stats['movies'],          'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>', 
             'bg' => 'bg-blue-500/20', 'text' => 'text-blue-400', 'border' => 'hover:border-blue-500/40', 'href' => null],
             
            ['label' => 'Tổng phim bộ',   'value' => $stats['tv_shows'],        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>', 
             'bg' => 'bg-indigo-500/20', 'text' => 'text-indigo-400', 'border' => 'hover:border-indigo-500/40', 'href' => null],
             
            ['label' => 'Tổng đánh giá',    'value' => $stats['reviews'],         'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>', 
             'bg' => 'bg-amber-500/20', 'text' => 'text-amber-400', 'border' => 'hover:border-amber-500/40', 'href' => null],
             
            ['label' => 'Tổng người dùng',  'value' => $stats['users'],           'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>',      
             'bg' => 'bg-sky-500/20', 'text' => 'text-sky-400', 'border' => 'hover:border-sky-500/40', 'href' => null],
             
            ['label' => 'Đánh giá hôm nay', 'value' => $stats['today_reviews'],   'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>',                                                                                                           
             'bg' => 'bg-emerald-500/20', 'text' => 'text-emerald-400', 'border' => 'hover:border-emerald-500/40', 'href' => null],
             
            ['label' => 'Báo cáo chờ',      'value' => $stats['pending_reports'], 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>',                                                       
             'bg' => $stats['pending_reports'] > 0 ? 'bg-orange-500/20' : 'bg-slate-500/20', 
             'text' => $stats['pending_reports'] > 0 ? 'text-orange-400' : 'text-slate-400', 
             'border' => $stats['pending_reports'] > 0 ? 'hover:border-orange-500/40' : 'hover:border-slate-500/40',
             'href' => null],
             
            ['label' => 'Diễn đàn',         'value' => $stats['forum_threads'],   'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/>',                                                               
             'bg' => 'bg-violet-500/20', 'text' => 'text-violet-400', 'border' => 'hover:border-violet-500/40', 'href' => null],
        ];
    @endphp

    @foreach($cards as $card)
        @php $wrap = $card['href'] ? 'a' : 'div'; @endphp
        <{{ $wrap }} @if($card['href']) href="{{ $card['href'] }}" @endif class="card p-5 {{ $card['href'] ? $card['border'] . ' transition-colors cursor-pointer' : '' }}">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-dark-400">{{ $card['label'] }}</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ number_format($card['value']) }}</p>
                    @if($card['href'] && $card['value'] > 0)
                        <p class="text-xs {{ $card['text'] }} mt-1">Nhấn để xem →</p>
                    @endif
                </div>
                <div class="w-12 h-12 rounded-xl {{ $card['bg'] }} flex items-center justify-center">
                    <svg class="w-6 h-6 {{ $card['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $card['icon'] !!}</svg>
                </div>
            </div>
        </{{ $wrap }}>
    @endforeach
</div>

{{-- ── Biểu đồ ────────────────────────────────────────────────── --}}
<div class="card p-5 mb-8 min-w-0 w-full overflow-hidden">
    <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
        <h2 class="font-semibold text-white flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
            {{ $chartTitle }}
        </h2>
        {{-- Period Filter --}}
        <div class="flex items-center gap-1 bg-dark-800 rounded-xl p-1">
            @foreach(['week' => '7 ngày', 'month' => 'Tháng', 'quarter' => 'Quý'] as $p => $label)
                <a href="{{ route('admin.dashboard', array_merge(request()->query(), ['period' => $p])) }}"
                   class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all {{ $period === $p ? 'bg-indigo-500 text-white shadow-sm' : 'text-dark-400 hover:text-white hover:bg-dark-700' }}">
                    {{ $label }}
                </a>
            @endforeach
            <select onchange="window.location.href=this.value" class="pl-3 pr-8 py-1.5 text-xs font-semibold rounded-lg bg-dark-800 border-none text-dark-400 hover:text-white hover:bg-dark-700 focus:outline-none focus:ring-0 transition-all cursor-pointer {{ $period === 'year' || is_numeric($period) ? 'bg-indigo-500 text-white shadow-sm' : '' }}">
                <option value="{{ route('admin.dashboard', array_merge(request()->query(), ['period' => 'year'])) }}" {{ $period === 'year' || $period == date('Y') ? 'selected' : '' }}>Năm nay</option>
                @foreach($availableYears as $year)
                    @if($year != date('Y'))
                        <option value="{{ route('admin.dashboard', array_merge(request()->query(), ['period' => $year])) }}" {{ $period == $year ? 'selected' : '' }}>Năm {{ $year }}</option>
                    @endif
                @endforeach
            </select>
        </div>
    </div>
    <div class="relative w-full h-64">
        <canvas id="dashboardChart"></canvas>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-6">
    {{-- ── Today's Reviews ────────────────────────────────────── --}}
    <div id="today-reviews" class="card flex flex-col">
        <div class="p-5 border-b border-dark-700 flex justify-between items-center">
            <h2 class="font-semibold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                Đánh giá hôm nay
            </h2>
            <span class="badge badge-sky">{{ $todayReviews->total() }}</span>
        </div>
        <div class="divide-y divide-dark-800 flex-1">
            @forelse($todayReviews as $review)
                <div class="px-4 py-3 flex items-center justify-between hover:bg-dark-800/30 transition-colors gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-slate-500 to-slate-700 flex items-center justify-center overflow-hidden shrink-0 text-xs font-bold text-white">
                            {{ mb_strtoupper(mb_substr($review->user->name ?? '?', 0, 1, 'UTF-8'), 'UTF-8') }}
                        </div>
                        <div class="flex-1 min-w-0">
                            @php $media = $review->movie ?? $review->tvShow; @endphp
                            {{-- User → Media --}}
                            <p class="text-sm text-white truncate">
                                <span class="font-medium">{{ $review->user->name ?? 'Ẩn danh' }}</span>
                                <span class="text-dark-500 mx-1">→</span>
                                <span class="text-dark-300">{{ $media->title ?? '—' }}</span>
                                @if($review->tvShow) <span class="text-[10px] text-indigo-400 ml-1">[bộ]</span> @endif
                            </p>
                            {{-- Excerpt preview (1 line) --}}
                            @php $preview = $review->excerpt ?: ($review->content ? Str::limit(strip_tags($review->content), 80) : null); @endphp
                            @if($preview)
                                <p class="text-xs text-dark-500 truncate mt-0.5">{{ $preview }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="text-xs font-bold text-white whitespace-nowrap">{{ $review->rating }}/10</span>
                        @if($media)
                        @php
                            $reviewUrl = $review->movie
                                ? route('movies.show', $review->movie->slug)
                                : route('tv-shows.show', $review->tvShow->slug);
                        @endphp
                        <a href="{{ $reviewUrl }}#review-{{ $review->id }}" target="_blank"
                           class="text-[11px] text-sky-400 hover:text-sky-300 transition-colors whitespace-nowrap">Xem →</a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-dark-500 text-sm">Chưa có đánh giá nào hôm nay.</div>
            @endforelse
        </div>
        @if($todayReviews->hasPages())
            <div class="p-3 border-t border-dark-700 bg-dark-900/50">
                {{ $todayReviews->appends(request()->except('reviews_page'))->fragment('today-reviews')->links('vendor.pagination.dashboard') }}
            </div>
        @endif
    </div>

    {{-- ── Today's Users ──────────────────────────────────────── --}}
    <div id="today-users" class="card flex flex-col">
        <div class="p-5 border-b border-dark-700 flex justify-between items-center">
            <h2 class="font-semibold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                Người dùng mới hôm nay
            </h2>
            <span class="badge badge-sky">{{ $todayUsers->total() }}</span>
        </div>
        <div class="divide-y divide-dark-800 flex-1">
            @forelse($todayUsers as $user)
                <div class="p-4 flex items-center gap-3 hover:bg-dark-800/30 transition-colors">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-sky-500 to-sky-700 flex items-center justify-center overflow-hidden shrink-0 ring-2 ring-dark-700">
                        @if($user->avatar)
                            <img src="{{ $user->avatar }}" alt="" class="w-full h-full object-cover" loading="lazy">
                        @else
                            <span class="text-xs font-bold text-white">{{ mb_strtoupper(mb_substr($user->name, 0, 1, 'UTF-8'), 'UTF-8') }}</span>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ $user->name }}</p>
                        <p class="text-xs text-dark-500 truncate">{{ $user->email }}</p>
                    </div>
                    <span class="badge text-[10px] bg-{{ $user->role->color() }}-500/20 text-{{ $user->role->color() }}-400">
                        {{ $user->role->label() }}
                    </span>
                </div>
            @empty
                <div class="p-8 text-center text-dark-500 text-sm">Chưa có người dùng mới nào hôm nay.</div>
            @endforelse
        </div>
        @if($todayUsers->hasPages())
            <div class="p-3 border-t border-dark-700 bg-dark-900/50">
                {{ $todayUsers->appends(request()->except('users_page'))->fragment('today-users')->links('vendor.pagination.dashboard') }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Chart.Tooltip.positioners.smart = function(elements, eventPosition) {
            if (!elements.length) return false;
            const pos = Chart.Tooltip.positioners.average(elements, eventPosition);
            if (!pos) return false;
            const chartArea = this.chart.chartArea;
            const xPercent = (pos.x - chartArea.left) / chartArea.width;
            return {
                x: pos.x,
                y: pos.y,
                xAlign: xPercent < 0.3 ? 'left' : (xPercent > 0.7 ? 'right' : 'center'),
                yAlign: 'bottom'
            };
        };

        const ctx = document.getElementById('dashboardChart').getContext('2d');
        
        let gradientReviews = ctx.createLinearGradient(0, 0, 0, 300);
        gradientReviews.addColorStop(0, 'rgba(251, 191, 36, 0.4)');
        gradientReviews.addColorStop(1, 'rgba(251, 191, 36, 0.0)');
        
        let gradientUsers = ctx.createLinearGradient(0, 0, 0, 300);
        gradientUsers.addColorStop(0, 'rgba(56, 189, 248, 0.4)');
        gradientUsers.addColorStop(1, 'rgba(56, 189, 248, 0.0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [
                    {
                        label: 'Đánh giá mới',
                        data: {!! json_encode($reviewsData) !!},
                        borderColor: '#fbbf24', // amber-400
                        backgroundColor: gradientReviews,
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#18181b',
                        pointBorderColor: '#fbbf24',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Người dùng mới',
                        data: {!! json_encode($usersData) !!},
                        borderColor: '#38bdf8', // sky-400
                        backgroundColor: gradientUsers,
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#18181b',
                        pointBorderColor: '#38bdf8',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        labels: { color: '#9ca3af', font: { family: "'Inter', sans-serif" }, usePointStyle: true, boxWidth: 20, padding: 20 }
                    },
                    tooltip: {
                        position: 'smart',
                        backgroundColor: 'rgba(24, 24, 27, 0.9)',
                        titleColor: '#fff',
                        bodyColor: '#cbd5e1',
                        borderColor: '#3f3f46',
                        borderWidth: 1,
                        padding: 12,
                        usePointStyle: true,
                        boxPadding: 4,
                    }
                },
                scales: {
                    x: {
                        grid: { color: '#27272a', drawBorder: false },
                        ticks: { color: '#9ca3af', font: { family: "'Inter', sans-serif" } }
                    },
                    y: {
                        grid: { color: '#27272a', drawBorder: false },
                        ticks: { 
                            color: '#9ca3af', 
                            font: { family: "'Inter', sans-serif" },
                            precision: 0 
                        },
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>

<script>
    /**
     * AJAX Pagination for dashboard widgets.
     * Intercepts clicks inside #today-reviews and #today-users pagination,
     * fetches the new page and swaps only the list + pagination content —
     * the chart above is never touched.
     */
    (function () {
        function initAjaxPagination(cardId, listSelector, paginationSelector) {
            const card = document.getElementById(cardId);
            if (!card) return;

            let isLoading = false;

            function setLoading(state) {
                isLoading = state;
                const list = card.querySelector(listSelector);
                const nav  = card.querySelector('nav[aria-label="Pagination"]');
                if (list) list.style.opacity = state ? '0.4' : '1';
                if (list) list.style.pointerEvents = state ? 'none' : '';
                if (nav)  nav.style.opacity = state ? '0.5' : '1';
                if (nav)  nav.style.pointerEvents = state ? 'none' : '';
                if (nav)  nav.style.cursor = state ? 'not-allowed' : '';
            }

            card.addEventListener('click', function (e) {
                if (isLoading) { e.preventDefault(); return; }

                const link = e.target.closest('a[href]');
                if (!link) return;
                if (!link.closest('nav[aria-label="Pagination"]')) return;

                e.preventDefault();
                const url = link.href;

                setLoading(true);

                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newCard = doc.getElementById(cardId);
                    if (!newCard) return;

                    const newList = newCard.querySelector(listSelector);
                    const curList = card.querySelector(listSelector);
                    if (newList && curList) curList.outerHTML = newList.outerHTML;

                    const newPag = newCard.querySelector(paginationSelector);
                    const curPag = card.querySelector(paginationSelector);
                    if (curPag) {
                        if (newPag) curPag.outerHTML = newPag.outerHTML;
                        else curPag.remove();
                    } else if (newPag) {
                        card.appendChild(newPag);
                    }

                    history.replaceState(null, '', url.split('#')[0] + '#' + cardId);
                })
                .catch(() => { window.location.href = url; })
                .finally(() => setLoading(false));
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            initAjaxPagination('today-reviews', '.divide-y.divide-dark-800.flex-1', '.p-3.border-t');
            initAjaxPagination('today-users',   '.divide-y.divide-dark-800.flex-1', '.p-3.border-t');
        });
    })();
</script>
@endpush

</x-admin-layout>
