<x-admin-layout title="Báo cáo" pageTitle="Quản lý báo cáo">

{{-- ── Summary chips ───────────────────────────────────────────────── --}}
@php
    $statusCounts = \App\Models\Report::selectRaw('status, count(*) as total')
        ->groupBy('status')
        ->pluck('total', 'status');
    $chips = [
        ['key' => '',          'label' => 'Tất cả',      'color' => 'slate'],
        ['key' => 'pending',   'label' => 'Chờ xử lý',   'color' => 'amber'],
        ['key' => 'resolved',  'label' => 'Đã xử lý',    'color' => 'emerald'],
        ['key' => 'dismissed', 'label' => 'Đã bác bỏ',   'color' => 'slate'],
    ];
    $totalAll = $statusCounts->sum();
@endphp

<div class="flex flex-wrap gap-2 mb-6">
    @foreach($chips as $chip)
        @php
            $isActive = request('status', '') === $chip['key'];
            $count    = $chip['key'] === '' ? $totalAll : ($statusCounts[$chip['key']] ?? 0);
        @endphp
        <a href="{{ route('admin.reports.index', array_merge(request()->except('status', 'page'), $chip['key'] ? ['status' => $chip['key']] : [])) }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border transition-colors
                  {{ $isActive
                      ? 'bg-'.$chip['color'].'-500/20 text-'.$chip['color'].'-300 border-'.$chip['color'].'-500/40'
                      : 'bg-dark-800 text-dark-400 border-dark-700 hover:text-white hover:border-dark-500' }}">
            {{ $chip['label'] }}
            <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold
                         {{ $isActive ? 'bg-'.$chip['color'].'-500/30' : 'bg-dark-700' }}">
                {{ $count }}
            </span>
        </a>
    @endforeach
</div>

{{-- ── Filters ─────────────────────────────────────────────────────── --}}
<form action="{{ route('admin.reports.index') }}" method="GET" class="mb-6 flex flex-col sm:flex-row gap-3">
    @if(request('status'))
        <input type="hidden" name="status" value="{{ request('status') }}">
    @endif

    <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm theo tên user, lý do..."
           class="input-dark text-sm flex-1 py-2.5">

    <select name="type" class="input-dark text-sm w-44 py-2.5">
        <option value="">Tất cả loại</option>
        <option value="review"          {{ request('type') === 'review'          ? 'selected' : '' }}>Đánh giá (Review)</option>
        <option value="comment"         {{ request('type') === 'comment'         ? 'selected' : '' }}>Bình luận phim</option>
        <option value="forum_thread"    {{ request('type') === 'forum_thread'    ? 'selected' : '' }}>Chủ đề diễn đàn</option>
        <option value="forum_reply"     {{ request('type') === 'forum_reply'     ? 'selected' : '' }}>Trả lời diễn đàn</option>
        <option value="article_comment" {{ request('type') === 'article_comment' ? 'selected' : '' }}>Bình luận tin tức</option>
    </select>

    <button type="submit" class="btn-secondary py-2.5 px-5 text-sm">Lọc</button>
    @if(request()->hasAny(['q','type','status']))
        <a href="{{ route('admin.reports.index') }}" class="btn-secondary py-2.5 px-5 text-sm">Xóa lọc</a>
    @endif
</form>

{{-- ── Table ───────────────────────────────────────────────────────── --}}
<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-dark-700 text-dark-400 text-left">
                    <th class="px-5 py-3 font-medium">Người báo cáo</th>
                    <th class="px-5 py-3 font-medium">Loại nội dung</th>
                    <th class="px-5 py-3 font-medium">Lý do</th>
                    <th class="px-5 py-3 font-medium">Mô tả</th>
                    <th class="px-5 py-3 font-medium">Trạng thái</th>
                    <th class="px-5 py-3 font-medium">Ngày</th>
                    <th class="px-5 py-3 font-medium text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-dark-800">
                @forelse($reports as $report)
                    @php
                        // Resolve type short label
                        $typeLabels = [
                            'App\\Models\\Review'         => ['label' => 'Đánh giá',       'color' => 'amber'],
                            'App\\Models\\Comment'        => ['label' => 'Bình luận phim',  'color' => 'sky'],
                            'App\\Models\\ForumThread'    => ['label' => 'Chủ đề diễn đàn','color' => 'violet'],
                            'App\\Models\\ForumReply'     => ['label' => 'Trả lời ĐĐ',     'color' => 'purple'],
                            'App\\Models\\ArticleComment' => ['label' => 'BL Tin tức',      'color' => 'teal'],
                        ];
                        $typeMeta  = $typeLabels[$report->reportable_type] ?? ['label' => class_basename($report->reportable_type), 'color' => 'slate'];

                        // Status badge
                        $statusMeta = [
                            'pending'   => ['label' => 'Chờ xử lý', 'color' => 'amber'],
                            'resolved'  => ['label' => 'Đã xử lý',  'color' => 'emerald'],
                            'dismissed' => ['label' => 'Bác bỏ',    'color' => 'slate'],
                        ][$report->status] ?? ['label' => $report->status, 'color' => 'gray'];

                        // Snippet of reported content
                        $snippet = null;
                        if ($report->reportable) {
                            $rp = $report->reportable;
                            $snippet = $rp->content ?? $rp->body ?? $rp->title ?? null;
                            if ($snippet) $snippet = Str::limit(strip_tags($snippet), 80);
                        }
                    @endphp
                    <tr class="hover:bg-dark-800/30 transition-colors {{ $report->status === 'pending' ? 'border-l-2 border-l-amber-500/60' : '' }}">

                        {{-- Reporter --}}
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-slate-500 to-slate-700 flex items-center justify-center overflow-hidden shrink-0 text-xs font-bold text-white">
                                    @if($report->user?->avatar)
                                        <img src="{{ $report->user->avatar }}" class="w-full h-full object-cover" alt="">
                                    @else
                                        {{ strtoupper(substr($report->user?->name ?? '?', 0, 1)) }}
                                    @endif
                                </div>
                                <span class="font-medium text-white whitespace-nowrap">{{ $report->user?->name ?? 'Ẩn danh' }}</span>
                            </div>
                        </td>

                        {{-- Type --}}
                        <td class="px-5 py-3">
                            <div class="space-y-1">
                                <span class="badge text-[10px] bg-{{ $typeMeta['color'] }}-500/20 text-{{ $typeMeta['color'] }}-400">
                                    {{ $typeMeta['label'] }}
                                </span>
                                @if($snippet)
                                    <p class="text-xs text-dark-500 max-w-[180px] truncate" title="{{ strip_tags($report->reportable->content ?? $report->reportable->body ?? $report->reportable->title ?? '') }}">
                                        {{ $snippet }}
                                    </p>
                                @else
                                    <p class="text-xs text-dark-600 italic">Nội dung đã bị xóa</p>
                                @endif
                            </div>
                        </td>

                        {{-- Reason --}}
                        <td class="px-5 py-3">
                            <span class="text-white">{{ $report->reason }}</span>
                        </td>

                        {{-- Description --}}
                        <td class="px-5 py-3 text-dark-400 max-w-[200px]">
                            @if($report->description)
                                <p class="truncate text-xs" title="{{ $report->description }}">{{ $report->description }}</p>
                            @else
                                <span class="text-dark-600 text-xs italic">—</span>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td class="px-5 py-3">
                            <span class="badge text-[10px] bg-{{ $statusMeta['color'] }}-500/20 text-{{ $statusMeta['color'] }}-400">
                                {{ $statusMeta['label'] }}
                            </span>
                            @if($report->is_public)
                                <span class="badge text-[10px] bg-blue-500/20 text-blue-400 mt-1 block w-fit">Public</span>
                            @endif
                        </td>

                        {{-- Date --}}
                        <td class="px-5 py-3 text-dark-500 text-xs whitespace-nowrap">
                            {{ $report->created_at->format('d/m/Y') }}
                            <span class="block text-dark-600">{{ $report->created_at->diffForHumans() }}</span>
                        </td>

                        {{-- Actions --}}
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end gap-1">

                                @if($report->status === 'pending')
                                    {{-- Resolve --}}
                                    <form action="{{ route('admin.reports.resolve', $report) }}" method="POST">
                                        @csrf
                                        <button type="submit" title="Đánh dấu đã xử lý"
                                                class="text-dark-400 hover:text-emerald-400 transition-colors p-1.5 rounded-lg hover:bg-emerald-500/10">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                    </form>
                                    {{-- Dismiss --}}
                                    <form action="{{ route('admin.reports.dismiss', $report) }}" method="POST">
                                        @csrf
                                        <button type="submit" title="Bác bỏ báo cáo"
                                                class="text-dark-400 hover:text-amber-400 transition-colors p-1.5 rounded-lg hover:bg-amber-500/10">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                            </svg>
                                        </button>
                                    </form>
                                @else
                                    {{-- Reopen --}}
                                    <form action="{{ route('admin.reports.reopen', $report) }}" method="POST">
                                        @csrf
                                        <button type="submit" title="Mở lại báo cáo"
                                                class="text-dark-400 hover:text-sky-400 transition-colors p-1.5 rounded-lg hover:bg-sky-500/10">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                        </button>
                                    </form>
                                @endif

                                {{-- Delete --}}
                                <form action="{{ route('admin.reports.destroy', $report) }}" method="POST"
                                      onsubmit="return confirm('Xóa báo cáo này?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" title="Xóa"
                                            class="text-dark-400 hover:text-red-400 transition-colors p-1.5 rounded-lg hover:bg-red-500/10">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-16 text-center">
                            <div class="flex flex-col items-center gap-3 text-dark-500">
                                <svg class="w-10 h-10 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
                                </svg>
                                <p class="text-sm">Không có báo cáo nào.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
<div class="mt-6">
    {{ $reports->links() }}
</div>

</x-admin-layout>
