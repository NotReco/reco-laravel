<x-admin-layout title="Danh hiệu" pageTitle="Quản lý danh hiệu">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-semibold text-white">Danh sách Danh hiệu</h2>
            <p class="text-sm text-dark-400 mt-1">Quản lý các danh hiệu để cấp cho người dùng — {{ $titles->total() }} danh hiệu</p>
        </div>
        <a href="{{ route('admin.user-titles.create') }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-sky-500 text-white text-sm font-semibold rounded-xl hover:bg-sky-600 transition-all shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Thêm danh hiệu
        </a>
    </div>

    {{-- Grid Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse ($titles as $title)
            <div class="bg-dark-900 border border-dark-800 rounded-2xl p-5 flex flex-col gap-4 hover:border-dark-600 transition-colors group">

                {{-- Preview badge --}}
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-slate-600 to-slate-800 flex items-center justify-center text-sm font-bold text-white shrink-0">
                        U
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="text-sm font-medium text-white">Người dùng</span>
                        <span class="text-xs font-bold px-2 py-0.5 rounded-full w-fit"
                              style="color: {{ $title->color_hex }}; background-color: {{ $title->color_hex }}22; border: 1px solid {{ $title->color_hex }}44">
                            {{ $title->name }}
                        </span>
                    </div>
                </div>

                {{-- Info --}}
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-4 h-4 rounded-full shrink-0" style="background-color: {{ $title->color_hex }}"></div>
                        <span class="text-xs text-dark-400 font-mono">{{ $title->color_hex }}</span>
                    </div>
                    @if($title->description)
                        <p class="text-xs text-dark-500 leading-relaxed line-clamp-2">{{ $title->description }}</p>
                    @else
                        <p class="text-xs text-dark-700 italic">Không có mô tả</p>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-between pt-3 border-t border-dark-800">
                    <div class="flex items-center gap-2">
                        @if($title->is_active)
                            <span class="inline-flex items-center gap-1 text-[10px] font-semibold px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block"></span>
                                Hiển thị
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-[10px] font-semibold px-2 py-0.5 rounded-full bg-red-500/10 text-red-400 border border-red-500/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-400 inline-block"></span>
                                Đang ẩn
                            </span>
                        @endif
                        <span class="text-[10px] text-dark-600">
                            {{ $title->users()->count() }} user
                        </span>
                    </div>

                    <div class="flex items-center gap-1">
                        <a href="{{ route('admin.user-titles.edit', $title) }}"
                           class="p-1.5 text-dark-500 hover:text-sky-400 hover:bg-sky-400/10 rounded-lg transition-colors" title="Sửa">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>
                        <form action="{{ route('admin.user-titles.destroy', $title) }}" method="POST" class="inline-block"
                              onsubmit="return confirm('Xóa danh hiệu này?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="p-1.5 text-dark-500 hover:text-red-400 hover:bg-red-400/10 rounded-lg transition-colors" title="Xóa">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-dark-800 mb-4">
                    <svg class="w-7 h-7 text-dark-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-white mb-1">Chưa có danh hiệu nào</h3>
                <p class="text-sm text-dark-500 mb-4">Hãy tạo danh hiệu đầu tiên để cấp cho người dùng.</p>
                <a href="{{ route('admin.user-titles.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-sky-500 text-white text-sm font-semibold rounded-xl hover:bg-sky-600 transition-all">
                    Thêm danh hiệu đầu tiên
                </a>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($titles->hasPages())
        <div class="mt-6">
            {{ $titles->links() }}
        </div>
    @endif

</x-admin-layout>
