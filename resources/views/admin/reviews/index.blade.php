<x-admin-layout title="Đánh giá" pageTitle="Quản lý đánh giá">

{{-- ── Filters ───────────────────────────────────────────────── --}}
<div class="mb-6 flex flex-col sm:flex-row gap-3">
    <form action="{{ route('admin.reviews.index') }}" method="GET" class="flex gap-3 flex-1">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm user hoặc phim..."
               class="input-dark text-sm flex-1 py-2.5">
        <select name="status" class="input-dark text-sm w-40 py-2.5">
            <option value="">Tất cả</option>
            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Đã duyệt</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Từ chối</option>
        </select>
        <button type="submit" class="btn-secondary py-2.5 px-5 text-sm">Lọc</button>
    </form>
</div>

{{-- ── Table ─────────────────────────────────────────────────── --}}
<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-dark-700 text-dark-400 text-left">
                    <th class="px-5 py-3 font-medium">User</th>
                    <th class="px-5 py-3 font-medium">Phim</th>
                    <th class="px-5 py-3 font-medium">Điểm</th>
                    <th class="px-5 py-3 font-medium">Nội dung</th>
                    <th class="px-5 py-3 font-medium">Trạng thái</th>
                    <th class="px-5 py-3 font-medium">Ngày</th>
                    <th class="px-5 py-3 font-medium text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-dark-800">
                @forelse($reviews as $review)
                    <tr class="hover:bg-dark-800/30 transition-colors">
                        <td class="px-5 py-3">
                            <span class="font-medium text-white">{{ $review->user->name ?? 'Ẩn danh' }}</span>
                        </td>
                        <td class="px-5 py-3 text-dark-300 max-w-[160px] truncate">{{ $review->movie->title ?? '—' }}</td>
                        <td class="px-5 py-3">
                            <span class="font-bold
                                @if($review->rating >= 9) text-yellow-400
                                @elseif($review->rating >= 7) text-emerald-400
                                @elseif($review->rating >= 5) text-orange-400
                                @else text-red-400
                                @endif">
                                {{ $review->rating }}/10
                            </span>
                        </td>
                        <td class="px-5 py-3 text-dark-400 max-w-[200px] truncate">
                            {{ $review->content ? Str::limit($review->content, 60) : '(Quick rating)' }}
                        </td>
                        <td class="px-5 py-3">
                            @php
                                $status = $review->status ?? 'approved';
                                $colors = ['approved' => 'emerald', 'pending' => 'amber', 'rejected' => 'red'];
                                $labels = ['approved' => 'Đã duyệt', 'pending' => 'Chờ duyệt', 'rejected' => 'Từ chối'];
                            @endphp
                            <span class="badge text-[10px] bg-{{ $colors[$status] ?? 'gray' }}-500/20 text-{{ $colors[$status] ?? 'gray' }}-400">
                                {{ $labels[$status] ?? $status }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-dark-500 text-xs">{{ $review->created_at->format('d/m/Y') }}</td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end gap-1">
                                @if(($review->status ?? 'approved') !== 'approved')
                                    <form action="{{ route('admin.reviews.approve', $review) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-dark-400 hover:text-emerald-400 transition-colors p-1" title="Duyệt">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                    </form>
                                @endif
                                @if(($review->status ?? 'approved') !== 'rejected')
                                    <form action="{{ route('admin.reviews.reject', $review) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-dark-400 hover:text-amber-400 transition-colors p-1" title="Từ chối">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST"
                                      onsubmit="return confirm('Xóa review này?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-dark-400 hover:text-red-400 transition-colors p-1" title="Xóa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-dark-500">Không có review nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $reviews->links() }}
</div>

</x-admin-layout>
