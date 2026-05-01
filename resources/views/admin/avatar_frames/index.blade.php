<x-admin-layout title="Khung Avatar" pageTitle="Quản lý khung Avatar">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-semibold text-white">Danh sách Khung Avatar</h2>
            <p class="text-sm text-dark-400 mt-1">Quản lý các viền avatar — {{ $frames->total() }} khung</p>
        </div>
        <a href="{{ route('admin.avatar-frames.create') }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-sky-500 text-white text-sm font-semibold rounded-xl hover:bg-sky-600 transition-all shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Thêm khung mới
        </a>
    </div>

    {{-- Grid --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-5">
        @forelse ($frames as $frame)
            <div class="bg-dark-900 border border-dark-800 rounded-2xl overflow-hidden hover:border-dark-600 transition-all group flex flex-col">

                {{-- Frame preview --}}
                <div class="relative flex items-center justify-center bg-gradient-to-br from-dark-800 to-dark-950 py-6 px-4">
                    {{-- Mock avatar underneath --}}
                    <div class="relative w-20 h-20 shrink-0">
                        {{-- Avatar bg --}}
                        <div class="w-full h-full rounded-full bg-gradient-to-br from-sky-500 to-blue-700 flex items-center justify-center text-3xl font-bold text-white scale-[1.0475]">
                            A
                        </div>
                        {{-- Frame overlay --}}
                        <img src="{{ Storage::url($frame->image_path) }}"
                             alt="{{ $frame->name }}"
                             class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[126%] h-[126%] max-w-none object-contain pointer-events-none"
                             loading="lazy">
                    </div>

                    {{-- Status dot --}}
                    <div class="absolute top-2.5 right-2.5">
                        @if($frame->is_active)
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 block ring-2 ring-dark-900" title="Hoạt động"></span>
                        @else
                            <span class="w-2.5 h-2.5 rounded-full bg-red-400 block ring-2 ring-dark-900" title="Ẩn"></span>
                        @endif
                    </div>
                </div>

                {{-- Info & actions --}}
                <div class="p-3 flex flex-col gap-2 flex-1">
                    <p class="text-sm font-semibold text-white truncate text-center" title="{{ $frame->name }}">
                        {{ $frame->name }}
                    </p>

                    <div class="flex items-center justify-center gap-1.5 text-[10px] text-dark-500">
                        @if($frame->is_active)
                            <span class="text-emerald-400">● Hiển thị</span>
                        @else
                            <span class="text-red-400">● Ẩn</span>
                        @endif
                        <span>·</span>
                        <span>{{ $frame->users()->count() }} user</span>
                    </div>

                    <div class="flex items-center justify-center gap-1 mt-auto pt-1 border-t border-dark-800">
                        <a href="{{ route('admin.avatar-frames.edit', $frame) }}"
                           class="flex-1 flex items-center justify-center gap-1 py-1.5 text-xs text-dark-400 hover:text-sky-400 hover:bg-sky-400/10 rounded-lg transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Sửa
                        </a>
                        <form action="{{ route('admin.avatar-frames.destroy', $frame) }}" method="POST"
                              onsubmit="return confirm('Xóa khung này?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="flex items-center justify-center gap-1 px-3 py-1.5 text-xs text-dark-400 hover:text-red-400 hover:bg-red-400/10 rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Xóa
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-dark-800 mb-4">
                    <svg class="w-8 h-8 text-dark-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-white mb-1">Chưa có khung avatar nào</h3>
                <p class="text-sm text-dark-500 mb-4">Tải lên khung avatar đầu tiên để trang trí hồ sơ người dùng.</p>
                <a href="{{ route('admin.avatar-frames.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-sky-500 text-white text-sm font-semibold rounded-xl hover:bg-sky-600 transition-all">
                    Thêm khung đầu tiên
                </a>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($frames->hasPages())
        <div class="mt-6">
            {{ $frames->links() }}
        </div>
    @endif

</x-admin-layout>
