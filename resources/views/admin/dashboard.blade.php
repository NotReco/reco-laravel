<x-admin-layout :title="'Dashboard'" pageTitle="Dashboard">

{{-- ── Stats Cards ───────────────────────────────────────────── --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    @php
        $cards = [
            ['label' => 'Tổng phim', 'value' => $stats['movies'], 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>', 'color' => 'rose'],
            ['label' => 'Tổng reviews', 'value' => $stats['reviews'], 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>', 'color' => 'amber'],
            ['label' => 'Tổng users', 'value' => $stats['users'], 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>', 'color' => 'sky'],
            ['label' => 'Reviews hôm nay', 'value' => $stats['today_reviews'], 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>', 'color' => 'emerald'],
        ];
    @endphp

    @foreach($cards as $card)
        <div class="card p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-dark-400">{{ $card['label'] }}</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ number_format($card['value']) }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-{{ $card['color'] }}-500/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-{{ $card['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $card['icon'] !!}</svg>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="grid lg:grid-cols-3 gap-6">
    {{-- ── Recent Reviews ────────────────────────────────────── --}}
    <div class="lg:col-span-2 card">
        <div class="p-5 border-b border-dark-700">
            <h2 class="font-semibold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                Reviews gần đây
            </h2>
        </div>
        <div class="divide-y divide-dark-800">
            @forelse($recentReviews as $review)
                <div class="p-4 flex items-center gap-4 hover:bg-dark-800/30 transition-colors">
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
                    <div class="shrink-0">
                        <span class="px-2 py-1 rounded-lg text-xs font-bold
                            @if($review->rating >= 9) bg-yellow-500/20 text-yellow-400
                            @elseif($review->rating >= 7) bg-emerald-500/20 text-emerald-400
                            @elseif($review->rating >= 5) bg-orange-500/20 text-orange-400
                            @else bg-red-500/20 text-red-400
                            @endif">
                            {{ $review->rating }}/10
                        </span>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-dark-500 text-sm">Chưa có review nào.</div>
            @endforelse
        </div>
    </div>

    {{-- ── Recent Users ──────────────────────────────────────── --}}
    <div class="card">
        <div class="p-5 border-b border-dark-700">
            <h2 class="font-semibold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                Users mới
            </h2>
        </div>
        <div class="divide-y divide-dark-800">
            @forelse($recentUsers as $user)
                <div class="p-4 flex items-center gap-3 hover:bg-dark-800/30 transition-colors">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-rose-500 to-rose-700 flex items-center justify-center overflow-hidden shrink-0 ring-2 ring-dark-700">
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
                <div class="p-8 text-center text-dark-500 text-sm">Chưa có user nào.</div>
            @endforelse
        </div>
    </div>
</div>

</x-admin-layout>
