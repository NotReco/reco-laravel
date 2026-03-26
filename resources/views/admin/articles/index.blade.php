<x-admin-layout :title="'Tin tức'" pageTitle="Quản lý tin tức">

{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <p class="text-dark-400 text-sm">Tổng: {{ $articles->total() }} bài viết</p>
    <a href="{{ route('admin.articles.create') }}"
       class="flex items-center gap-2 px-4 py-2 bg-rose-600 text-white text-sm font-medium rounded-xl hover:bg-rose-700 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Viết bài mới
    </a>
</div>

{{-- Table --}}
<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-dark-800 text-left">
                    <th class="px-5 py-3 text-dark-400 font-medium">Tiêu đề</th>
                    <th class="px-5 py-3 text-dark-400 font-medium">Tác giả</th>
                    <th class="px-5 py-3 text-dark-400 font-medium">Tags</th>
                    <th class="px-5 py-3 text-dark-400 font-medium text-center">Trạng thái</th>
                    <th class="px-5 py-3 text-dark-400 font-medium text-center">Bình luận</th>
                    <th class="px-5 py-3 text-dark-400 font-medium">Ngày tạo</th>
                    <th class="px-5 py-3 text-dark-400 font-medium text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-dark-800">
                @forelse($articles as $article)
                    <tr class="hover:bg-dark-800/30 transition-colors">
                        <td class="px-5 py-3">
                            <div class="max-w-xs">
                                <p class="text-white font-medium truncate">{{ $article->title }}</p>
                                @if($article->subtitle)
                                    <p class="text-dark-500 text-xs truncate mt-0.5">{{ $article->subtitle }}</p>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="text-dark-300">{{ $article->user->name ?? 'Ẩn danh' }}</span>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex flex-wrap gap-1">
                                @foreach($article->tags as $tag)
                                    <span class="px-1.5 py-0.5 bg-dark-700 text-dark-300 text-[10px] font-semibold rounded uppercase">{{ $tag->name }}</span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-5 py-3 text-center">
                            @if($article->is_published)
                                <span class="px-2 py-1 bg-emerald-500/20 text-emerald-400 text-xs font-bold rounded-lg">Đã đăng</span>
                            @else
                                <span class="px-2 py-1 bg-dark-700 text-dark-400 text-xs font-bold rounded-lg">Nháp</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-center text-dark-400">{{ $article->comments_count }}</td>
                        <td class="px-5 py-3 text-dark-500 text-xs">{{ $article->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('news.show', $article) }}" target="_blank"
                                   class="p-1.5 text-dark-500 hover:text-sky-400 transition-colors" title="Xem">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('admin.articles.edit', $article) }}"
                                   class="p-1.5 text-dark-500 hover:text-amber-400 transition-colors" title="Sửa">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="{{ route('admin.articles.destroy', $article) }}" method="POST"
                                      onsubmit="return confirm('Xóa bài viết này?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-dark-500 hover:text-red-400 transition-colors" title="Xóa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-dark-500">Chưa có bài viết nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($articles->hasPages())
        <div class="px-5 py-4 border-t border-dark-800">
            {{ $articles->links() }}
        </div>
    @endif
</div>

</x-admin-layout>
