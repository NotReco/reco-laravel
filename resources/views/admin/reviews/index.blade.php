<x-admin-layout title="Đánh giá bị báo cáo" pageTitle="Đánh giá bị báo cáo">

    {{-- ── Header Info ──────────────────────────────────────────────── --}}
    @if ($totalFlagged > 0)
        <div class="mb-5 flex items-center gap-3 px-4 py-3 rounded-xl bg-amber-500/10 border border-amber-500/25">
            <svg class="w-5 h-5 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9" />
            </svg>
            <p class="text-sm text-amber-300">
                Có <span class="font-bold text-amber-200">{{ $totalFlagged }}</span> đánh giá đang chờ xử lý.
                Các đánh giá bên dưới đã được cộng đồng báo cáo — hãy xem xét và quyết định.
            </p>
        </div>
    @else
        <div class="mb-5 flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-500/10 border border-emerald-500/25">
            <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <p class="text-sm text-emerald-300">Không có đánh giá nào cần xem xét. Cộng đồng đang hoạt động tốt!</p>
        </div>
    @endif

    {{-- ── Filters ───────────────────────────────────────────────────── --}}
    <form action="{{ route('admin.reviews.index') }}" method="GET" class="mb-6 flex flex-col sm:flex-row gap-3">
        <input type="text" name="q" value="{{ request('q') }}"
            placeholder="Tìm người dùng, phim hoặc TV series..." class="input-dark text-sm flex-1 py-2.5">
        <select name="type" class="input-dark text-sm w-44 py-2.5">
            <option value="">Tất cả loại</option>
            <option value="movie" {{ request('type') === 'movie' ? 'selected' : '' }}>Phim</option>
            <option value="series" {{ request('type') === 'series' ? 'selected' : '' }}>TV Series</option>
        </select>
        <button type="submit" class="btn-secondary py-2.5 px-5 text-sm">Lọc</button>
        @if (request()->hasAny(['q', 'type']))
            <a href="{{ route('admin.reviews.index') }}" class="btn-secondary py-2.5 px-5 text-sm">Xóa lọc</a>
        @endif
    </form>

    {{-- ── Table ─────────────────────────────────────────────────────── --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-dark-700 text-dark-400 text-left">
                        <th class="px-5 py-3 font-medium">Người dùng</th>
                        <th class="px-5 py-3 font-medium">Phim / TV Series</th>
                        <th class="px-5 py-3 font-medium">Điểm</th>
                        <th class="px-5 py-3 font-medium">Nội dung đánh giá</th>
                        <th class="px-5 py-3 font-medium">Báo cáo</th>
                        <th class="px-5 py-3 font-medium">Trạng thái</th>
                        <th class="px-5 py-3 font-medium text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-dark-800">
                    @forelse($reviews as $review)
                        @php
                            $subject = $review->movie ?? $review->tvShow;
                            $subjectType = $review->movie ? 'Phim' : 'Series';
                            $topReport = $review->reports->first();

                            // Lý do báo cáo phổ biến nhất
                            $reasonCounts = $review->reports->groupBy('reason')->map->count()->sortDesc();
                            $topReason = $reasonCounts->keys()->first();
                            $topReasonCount = $reasonCounts->first();
                        @endphp
                        <tr class="hover:bg-dark-800/30 transition-colors group border-l-2 border-l-amber-500/50">

                            {{-- User --}}
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2.5">
                                    <div
                                        class="w-8 h-8 rounded-full bg-gradient-to-br from-sky-500 to-sky-700 flex items-center justify-center overflow-hidden shrink-0 text-xs font-bold text-white">
                                        @if ($review->user?->avatar)
                                            <img src="{{ $review->user->avatar }}" class="w-full h-full object-cover"
                                                alt="">
                                        @else
                                            {{ strtoupper(substr($review->user?->name ?? '?', 0, 1)) }}
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-semibold text-white text-xs leading-tight">
                                            {{ $review->user?->name ?? 'Ẩn danh' }}</p>
                                        <p class="text-dark-500 text-[10px]">{{ $review->created_at->format('d/m/Y') }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            {{-- Subject (phim/series) --}}
                            <td class="px-5 py-4">
                                <div class="flex items-start gap-2 max-w-[160px]">
                                    @if ($subject?->poster)
                                        <img src="{{ $subject->poster }}" alt=""
                                            class="w-8 h-11 object-cover rounded shrink-0 opacity-80">
                                    @endif
                                    <div class="min-w-0">
                                        <p class="text-white text-xs font-medium truncate leading-snug">
                                            {{ $subject?->title ?? '—' }}</p>
                                        <span
                                            class="text-[10px] px-1.5 py-0.5 rounded-md mt-0.5 inline-block
                                        {{ $review->movie ? 'bg-sky-500/15 text-sky-400' : 'bg-violet-500/15 text-violet-400' }}">
                                            {{ $subjectType }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            {{-- Rating --}}
                            <td class="px-5 py-4">
                                <span
                                    class="text-sm font-bold
                                @if ($review->rating >= 9) text-yellow-400
                                @elseif($review->rating >= 7) text-emerald-400
                                @elseif($review->rating >= 5) text-orange-400
                                @else text-red-400 @endif">
                                    {{ $review->rating }}<span class="text-dark-500 font-normal text-xs">/10</span>
                                </span>
                            </td>

                            {{-- Nội dung --}}
                            <td class="px-5 py-4">
                                @if ($review->content)
                                    <p class="text-dark-300 text-xs max-w-[200px] line-clamp-2 leading-relaxed"
                                        title="{{ strip_tags($review->content) }}">
                                        {{ Str::limit(strip_tags($review->content), 80) }}
                                    </p>
                                    @if ($review->is_spoiler)
                                        <span
                                            class="mt-1 inline-block text-[10px] px-1.5 py-0.5 rounded bg-orange-500/15 text-orange-400">⚠
                                            Spoiler</span>
                                    @endif
                                @else
                                    <span class="text-dark-600 text-xs italic">(Chỉ chấm điểm)</span>
                                @endif
                            </td>

                            {{-- Báo cáo --}}
                            <td class="px-5 py-4">
                                <div class="space-y-2">
                                    @foreach($review->reports->take(3) as $rpt)
                                        <div class="flex items-center gap-1.5 group/rpt" x-data="{ banOpen: false }">
                                            {{-- Trust badge --}}
                                            @if($rpt->user && $rpt->user->reputation_score < 20)
                                                <span title="Độ tin cậy thấp ({{ $rpt->user->reputation_score }} điểm)"
                                                    class="shrink-0 text-[9px] font-bold px-1 py-0.5 rounded bg-amber-500/20 text-amber-400 border border-amber-500/30">⚠️ Thấp</span>
                                            @endif
                                            <span class="text-[10px] text-dark-300 truncate max-w-[100px]" title="{{ $rpt->user?->name }}">{{ $rpt->user?->name ?? 'Ẩn danh' }}</span>
                                            <span class="text-[9px] text-dark-600 truncate max-w-[90px]">— {{ Str::limit($rpt->reason, 20) }}</span>

                                            {{-- Nút phạt thủ công --}}
                                            @if($rpt->user)
                                                <div class="relative ml-auto">
                                                    <button @click="banOpen = !banOpen"
                                                        title="Phạt người báo cáo"
                                                        class="opacity-0 group-hover/rpt:opacity-100 transition-opacity w-5 h-5 rounded flex items-center justify-center text-dark-500 hover:text-orange-400 hover:bg-orange-500/10">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                        </svg>
                                                    </button>
                                                    <div x-show="banOpen" @click.outside="banOpen = false" x-cloak
                                                        class="absolute right-0 top-6 z-50 w-44 bg-dark-800 border border-dark-600 rounded-xl shadow-xl py-1.5 text-xs">
                                                        <p class="px-3 py-1 text-dark-500 text-[10px] font-medium uppercase tracking-wider">Cấm báo cáo</p>
                                                        <form action="{{ route('admin.reports.banReporter', $rpt) }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="days" value="3">
                                                            <button type="submit" class="w-full text-left px-3 py-1.5 hover:bg-dark-700 text-orange-400 transition-colors">⚠️ Cấm 3 ngày</button>
                                                        </form>
                                                        <form action="{{ route('admin.reports.banReporter', $rpt) }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="days" value="7">
                                                            <button type="submit" class="w-full text-left px-3 py-1.5 hover:bg-dark-700 text-red-400 transition-colors">🚫 Cấm 7 ngày</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                    @if($review->pending_reports_count > 3)
                                        <p class="text-[10px] text-dark-600">+{{ $review->pending_reports_count - 3 }} báo cáo khác</p>
                                    @endif
                                </div>
                            </td>

                            {{-- Trạng thái review --}}
                            <td class="px-5 py-4">
                                @if ($review->status === 'hidden')
                                    <span class="badge text-[10px] bg-purple-500/20 text-purple-400">Đang ẩn</span>
                                @else
                                    <span class="badge text-[10px] bg-emerald-500/20 text-emerald-400">Hiển thị</span>
                                @endif
                            </td>

                            {{-- Thao tác --}}
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1">

                                    @if ($review->status === 'hidden')
                                        {{-- Bỏ ẩn --}}
                                        <form action="{{ route('admin.reviews.unhide', $review) }}" method="POST">
                                            @csrf
                                            <button type="submit" title="Bỏ ẩn đánh giá"
                                                class="text-dark-400 hover:text-emerald-400 transition-colors p-1.5 rounded-lg hover:bg-emerald-500/10">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                        </form>
                                    @else
                                        {{-- Ẩn đánh giá --}}
                                        <form id="hide-form-{{ $review->id }}"
                                            action="{{ route('admin.reviews.hide', $review) }}" method="POST">
                                            @csrf
                                            <button type="button" title="Ẩn đánh giá (vi phạm)"
                                                class="text-dark-400 hover:text-purple-400 transition-colors p-1.5 rounded-lg hover:bg-purple-500/10"
                                                @click="$dispatch('admin-confirm', {
                                                    title: 'Ẩn đánh giá?',
                                                    message: 'Đánh giá sẽ bị ẩn và tất cả báo cáo sẽ được đánh dấu đã xử lý.',
                                                    formId: 'hide-form-{{ $review->id }}',
                                                    confirmText: 'Ẩn đánh giá',
                                                    type: 'warning'
                                                })">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Bỏ qua báo cáo --}}
                                    <form id="dismiss-form-{{ $review->id }}"
                                        action="{{ route('admin.reviews.dismissReports', $review) }}" method="POST">
                                        @csrf
                                        <button type="button" title="Bỏ qua báo cáo — đánh giá hợp lệ"
                                            class="text-dark-400 hover:text-sky-400 transition-colors p-1.5 rounded-lg hover:bg-sky-500/10"
                                            @click="$dispatch('admin-confirm', {
                                                title: 'Bỏ qua báo cáo?',
                                                message: 'Các báo cáo sẽ bị bác bỏ. Đánh giá vẫn hiển thị bình thường.',
                                                formId: 'dismiss-form-{{ $review->id }}',
                                                confirmText: 'Bỏ qua',
                                                type: 'info'
                                            })">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                    </form>

                                    {{-- Xóa hẳn --}}
                                    <form id="delete-review-{{ $review->id }}"
                                        action="{{ route('admin.reviews.destroy', $review) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="button" title="Xóa đánh giá vĩnh viễn"
                                            class="text-dark-400 hover:text-red-400 transition-colors p-1.5 rounded-lg hover:bg-red-500/10"
                                            @click="$dispatch('admin-confirm', {
                                                title: 'Xóa đánh giá?',
                                                message: 'Hành động này không thể hoàn tác. Đánh giá và toàn bộ báo cáo sẽ bị xóa vĩnh viễn.',
                                                formId: 'delete-review-{{ $review->id }}',
                                                confirmText: 'Xóa vĩnh viễn',
                                                type: 'danger'
                                            })">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
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
                                    <svg class="w-12 h-12 opacity-30" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-sm font-medium text-dark-400">Không có đánh giá nào bị báo cáo</p>
                                    <p class="text-xs text-dark-600">Khi cộng đồng báo cáo một đánh giá, nó sẽ xuất
                                        hiện ở đây</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if ($reviews->hasPages())
        <div class="mt-6">
            {{ $reviews->links() }}
        </div>
    @endif

</x-admin-layout>
