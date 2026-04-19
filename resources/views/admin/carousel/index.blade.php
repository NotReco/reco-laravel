<x-admin-layout title="Quản lý Carousel" pageTitle="Carousel Trang Chủ">

<div class="max-w-5xl" x-data="{ addModalOpen: false, search: '' }">
    
    {{-- Top bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-white">Phim Trình Chiếu</h2>
            <p class="text-sm text-dark-400 mt-1">Tối đa 20 phim. Phim ở trên cùng sẽ hiển thị đầu tiên.</p>
        </div>

        <div class="flex items-center gap-3">
            <form action="{{ route('admin.carousel.autoUpdate') }}" method="POST">
                @csrf
                <button type="submit" class="btn-dark text-sm px-4 py-2" onclick="return confirm('Hành động này sẽ gỡ toàn bộ các phim đang được ghim và tự động lấy 20 phim hot nhất lên thế chỗ. Bạn chắc chứ?')">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Tự động Cập nhật
                </button>
            </form>
            
            <button @click="addModalOpen = true" class="btn-sky text-sm px-4 py-2">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Thêm Phim
            </button>
        </div>
    </div>

    {{-- Danh sách phim đang được ghim --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead>
                    <tr class="bg-dark-800/50 border-b border-dark-700/50">
                        <th class="px-5 py-3 font-medium text-dark-300 w-16">Thứ tự</th>
                        <th class="px-5 py-3 font-medium text-dark-300">Bộ phim</th>
                        <th class="px-5 py-3 font-medium text-dark-300">Thể loại</th>
                        <th class="px-5 py-3 font-medium text-dark-300 text-right w-32">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-dark-800">
                    @forelse($featuredMovies as $index => $movie)
                        <tr class="hover:bg-dark-800/50 transition-colors">
                            <td class="px-5 py-3 text-center">
                                <span class="inline-flex w-7 h-7 items-center justify-center rounded-full bg-dark-700 text-white font-bold text-xs">{{ $index + 1 }}</span>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-16 rounded overflow-hidden bg-dark-700 shrink-0">
                                        @if($movie->poster)
                                            <img src="{{ $movie->poster }}" class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-medium text-white truncate text-base">{{ $movie->title }}</p>
                                        <p class="text-xs text-dark-400 mt-1">Lượt xem: {{ number_format($movie->view_count) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-dark-400">
                                <span class="truncate block max-w-[200px]">{{ $movie->genres->pluck('name')->implode(', ') }}</span>
                            </td>
                            <td class="px-5 py-3 text-right space-x-1">
                                {{-- Up Arrow --}}
                                <form action="{{ route('admin.carousel.moveUp', $movie) }}" method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit" @disabled($loop->first) class="p-1.5 text-dark-400 hover:text-white rounded disabled:opacity-30 disabled:cursor-not-allowed">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                    </button>
                                </form>
                                {{-- Down Arrow --}}
                                <form action="{{ route('admin.carousel.moveDown', $movie) }}" method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit" @disabled($loop->last) class="p-1.5 text-dark-400 hover:text-white rounded disabled:opacity-30 disabled:cursor-not-allowed">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                </form>
                                {{-- Remove --}}
                                <form action="{{ route('admin.carousel.destroy', $movie) }}" method="POST" class="inline-block ml-2">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-red-500 hover:text-red-400 rounded" title="Gỡ khỏi Carousel" onclick="return confirm('Gỡ phim này khỏi Carousel?')">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-dark-400">
                                <svg class="w-12 h-12 mx-auto text-dark-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Carousel đang trống. Hãy nhấn "Tự động Lấp Đầy" hoặc thêm phim thủ công.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


    {{-- MODAL CHỌN PHIM --}}
    <div x-show="addModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        {{-- Backdrop --}}
        <div x-show="addModalOpen"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-dark-950/80 backdrop-blur-sm transition-opacity" aria-hidden="true" @click="addModalOpen = false"></div>

        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
            <div x-show="addModalOpen"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative bg-dark-900 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-2xl w-full border border-dark-700 flex flex-col max-h-[85vh]">
                
                {{-- Modal Header --}}
                <div class="px-6 py-5 border-b border-dark-800 flex items-center justify-between shrink-0">
                    <div>
                        <h3 class="text-xl font-bold text-white" id="modal-title">Chọn phim ghim lên Carousel</h3>
                        <p class="text-sm text-dark-400 mt-1">Danh sách dưới đây là các phim thỏa mãn điều kiện có đầy đủ Ảnh, Nền và Trailer.</p>
                    </div>
                    <button type="button" class="text-dark-400 hover:text-white" @click="addModalOpen = false">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Modal Body - Search & List --}}
                <div class="p-6 flex-1 overflow-hidden flex flex-col">
                    <input type="text" x-model="search" placeholder="Tìm tên phim..." class="input-dark w-full mb-4 shrink-0">
                    
                    <div class="flex-1 overflow-y-auto pr-2 space-y-2 relative" style="min-h: 300px;">
                        @foreach($eligibleMovies as $movie)
                            <div class="flex items-center justify-between p-3 rounded-xl bg-dark-800 border border-dark-700/50 hover:border-dark-600 transition-colors"
                                 x-show="search === '' || '{{ strtolower(addslashes($movie->title)) }}'.includes(search.toLowerCase())">
                                <div class="flex items-center gap-4 min-w-0">
                                    <div class="w-12 h-16 rounded overflow-hidden bg-dark-900 shrink-0">
                                        <img src="{{ $movie->poster }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-white truncate">{{ $movie->title }}</p>
                                        <p class="text-xs text-dark-400 truncate">{{ $movie->genres->pluck('name')->implode(', ') }}</p>
                                    </div>
                                </div>
                                <form action="{{ route('admin.carousel.store') }}" method="POST" class="shrink-0 ml-4">
                                    @csrf
                                    <input type="hidden" name="movie_id" value="{{ $movie->id }}">
                                    <button type="submit" class="btn-sky text-xs px-3 py-1.5 rounded-lg">Chọn lên Carousel</button>
                                </form>
                            </div>
                        @endforeach

                        @if($eligibleMovies->isEmpty())
                            <div class="text-center py-12 text-dark-400">
                                <p>Không có phim nào đủ điều kiện hoặc chưa ghim.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</x-admin-layout>
