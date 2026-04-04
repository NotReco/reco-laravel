<x-admin-layout pageTitle="Quản lý chuyên mục diễn đàn">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-semibold text-white">Danh sách chuyên mục</h2>
            <p class="text-sm text-dark-400 mt-1">Quản lý các chuyên mục hiển thị trên diễn đàn</p>
        </div>
        <a href="{{ route('admin.forum-categories.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-sky-500 text-white text-sm font-semibold rounded-xl hover:bg-sky-600 transition-all shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Thêm chuyên mục
        </a>
    </div>

    <div class="bg-dark-900 border border-dark-800 rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-dark-800/50 text-dark-300">
                    <tr>
                        <th class="px-6 py-4 font-medium tracking-wider">Thông tin</th>
                        <th class="px-6 py-4 font-medium tracking-wider text-center">Thứ tự</th>
                        <th class="px-6 py-4 font-medium tracking-wider text-center">Trạng thái</th>
                        <th class="px-6 py-4 font-medium tracking-wider text-center">Bài viết</th>
                        <th class="px-6 py-4 font-medium tracking-wider text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-dark-800">
                    @forelse ($categories as $category)
                        <tr class="hover:bg-dark-800/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-semibold text-white">{{ $category->name }}</span>
                                    <span class="text-xs text-dark-400">{{ $category->slug }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 rounded bg-dark-800 text-dark-300 text-xs font-medium border border-dark-700">
                                    {{ $category->order }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($category->is_active)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        Đang hiển thị
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-500/10 text-red-400 border border-red-500/20">
                                        Đang ẩn
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-dark-300">{{ $category->threads_count }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.forum-categories.edit', $category) }}" 
                                       class="p-2 text-dark-400 hover:text-sky-400 hover:bg-sky-400/10 rounded-lg transition-colors" title="Sửa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    
                                    <form action="{{ route('admin.forum-categories.destroy', $category) }}" method="POST" class="inline-block"
                                          onsubmit="return confirm('Bạn có chắc muốn xóa chuyên mục này?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" 
                                                class="p-2 text-dark-400 hover:text-red-400 hover:bg-red-400/10 rounded-lg transition-colors" title="Xóa">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-dark-800 mb-4">
                                    <svg class="w-6 h-6 text-dark-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                </div>
                                <h3 class="text-sm font-medium text-white mb-1">Chưa có chuyên mục</h3>
                                <p class="text-sm text-dark-400">Hãy thêm chuyên mục diễn đàn đầu tiên của bạn.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($categories->hasPages())
            <div class="px-6 py-4 border-t border-dark-800">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
