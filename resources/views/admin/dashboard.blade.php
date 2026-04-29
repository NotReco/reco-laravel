<x-admin-layout :title="'Tổng quan'" pageTitle="Tổng quan">

{{-- ── Stats Cards ───────────────────────────────────────────── --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-8">
    @php
        $cards = [
            ['label' => 'Tổng phim',        'value' => $stats['movies'],          'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>', 
             'bg' => 'bg-blue-500/20', 'text' => 'text-blue-400', 'border' => 'hover:border-blue-500/40', 'href' => null],
             
            ['label' => 'Tổng TV Series',   'value' => $stats['tv_shows'],        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>', 
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
             
            ['label' => 'Diễn đàn',         'value' => $stats['forum_threads'],   'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a2 2 0 01-2-2V10a2 2 0 012-2"/>',                                                               
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

{{-- ── Biểu đồ 7 ngày ────────────────────────────────────── --}}
<div class="card p-5 mb-8 min-w-0 w-full overflow-hidden">
    <div class="flex items-center justify-between mb-4">
        <h2 class="font-semibold text-white flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
            Biến động 7 ngày qua
        </h2>
    </div>
    <div class="relative w-full h-64">
        <canvas id="dashboardChart"></canvas>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-6">
    {{-- ── Today's Reviews ────────────────────────────────────── --}}
    <div class="card flex flex-col">
        <div class="p-5 border-b border-dark-700 flex justify-between items-center">
            <h2 class="font-semibold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                Đánh giá hôm nay
            </h2>
            <span class="badge badge-sky">{{ $todayReviews->total() }}</span>
        </div>
        <div class="divide-y divide-dark-800 flex-1">
            @forelse($todayReviews as $review)
                <div class="p-4 flex items-center justify-between hover:bg-dark-800/30 transition-colors">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-slate-500 to-slate-700 flex items-center justify-center overflow-hidden shrink-0 text-xs font-bold text-white">
                            {{ strtoupper(substr($review->user->name ?? '?', 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-white truncate">
                                <span class="font-medium">{{ $review->user->name ?? 'Ẩn danh' }}</span>
                                <span class="text-dark-500 mx-1">→</span>
                                <span class="text-dark-300">{{ $review->movie->title ?? '—' }}</span>
                            </p>
                            <p class="text-xs text-dark-500 mt-0.5">{{ $review->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 shrink-0 ml-4">
                        <span class="px-2 py-1 rounded-lg text-xs font-bold bg-dark-700 text-white">
                            {{ $review->rating }}/10
                        </span>
                        @if($review->movie)
                        <a href="{{ route('movies.show', $review->movie->slug) }}#review-{{ $review->id }}" target="_blank" class="px-3 py-1 rounded-lg text-xs font-medium bg-sky-600/20 text-sky-400 hover:bg-sky-600/30 transition-colors">
                            Xem
                        </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-dark-500 text-sm">Chưa có đánh giá nào hôm nay.</div>
            @endforelse
        </div>
        @if($todayReviews->hasPages())
            <div class="p-4 border-t border-dark-700 bg-dark-900/50">
                {{ $todayReviews->appends(request()->except('reviews_page'))->links() }}
            </div>
        @endif
    </div>

    {{-- ── Today's Users ──────────────────────────────────────── --}}
    <div class="card flex flex-col">
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
                            <span class="text-xs font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
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
            <div class="p-4 border-t border-dark-700 bg-dark-900/50">
                {{ $todayUsers->appends(request()->except('users_page'))->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('dashboardChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [
                    {
                        label: 'Đánh giá mới',
                        data: {!! json_encode($reviewsData) !!},
                        borderColor: '#fbbf24', // amber-400
                        backgroundColor: 'rgba(251, 191, 36, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Người dùng mới',
                        data: {!! json_encode($usersData) !!},
                        borderColor: '#38bdf8', // sky-400
                        backgroundColor: 'rgba(56, 189, 248, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: { color: '#9ca3af', font: { family: "'Inter', sans-serif" } }
                    }
                },
                scales: {
                    x: {
                        grid: { color: '#27272a' },
                        ticks: { color: '#9ca3af', font: { family: "'Inter', sans-serif" } }
                    },
                    y: {
                        grid: { color: '#27272a' },
                        ticks: { color: '#9ca3af', stepSize: 1, font: { family: "'Inter', sans-serif" } },
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
@endpush

</x-admin-layout>
