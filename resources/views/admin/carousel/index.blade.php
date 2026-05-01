<x-admin-layout title="Quản lý Carousel" pageTitle="Quản lý Carousel">

    {{-- Alpine data gồm danh sách items khởi tạo từ server --}}
    <div class="max-w-5xl" x-data="carouselManager()">

        {{-- Top bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-xl font-bold text-white">Nội Dung Trình Chiếu</h2>
                <p class="text-sm text-dark-400 mt-1">Tối đa 10 nội dung. Thứ tự càng nhỏ thì sẽ hiển thị đầu tiên.</p>
            </div>

            <div class="flex items-center gap-3">
                <form id="auto-update-form" action="{{ route('admin.carousel.autoUpdate') }}" method="POST">
                    @csrf
                    <button type="button"
                        @click="$dispatch('admin-confirm', { title: 'Tự động lấy nội dung hot', message: 'Hành động này sẽ gỡ toàn bộ nội dung đang được ghim và tự động lấy top 10 (5 Phim + 5 TV Series) hot nhất lên thế chỗ. Bạn chắc chứ?', formId: 'auto-update-form', confirmText: 'Cập nhật', type: 'info' })"
                        class="btn-secondary text-sm px-4 py-2">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Tự động Cập nhật
                    </button>
                </form>

                <button @click="addModalOpen = true" class="btn-sky text-sm px-4 py-2">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
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
                    <tbody class="divide-y divide-dark-800" id="carousel-tbody">
                        {{-- Sẽ được render bởi Alpine khi trang load --}}
                        <template x-if="items.length === 0">
                            <tr>
                                <td colspan="4" class="px-5 py-8 text-center text-dark-400">
                                    <svg class="w-12 h-12 mx-auto text-dark-600 mb-3" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Carousel đang trống. Hãy nhấn "Tự động Lấp Đầy" hoặc thêm phim thủ công.
                                </td>
                            </tr>
                        </template>
                        <template x-for="(item, index) in items" :key="item.type + '-' + item.id">
                            <tr class="hover:bg-dark-800/50 transition-colors"
                                :class="{ 'opacity-50 pointer-events-none': movingId === item.type + '-' + item.id }">
                                {{-- Thứ tự --}}
                                <td class="px-5 py-3 text-center">
                                    <span
                                        class="inline-flex w-7 h-7 items-center justify-center rounded-full bg-dark-700 text-white font-bold text-xs"
                                        x-text="index + 1"></span>
                                </td>
                                {{-- Poster + Tên --}}
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-16 rounded overflow-hidden bg-dark-700 shrink-0">
                                            <img :src="item.poster" class="w-full h-full object-cover"
                                                :alt="item.title">
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2">
                                                <p class="font-medium text-white truncate text-base"
                                                    x-text="item.title"></p>
                                                <template x-if="item.media_type === 'tv'">
                                                    <span
                                                        class="px-1.5 py-0.5 rounded bg-dark-700 text-[10px] font-bold text-dark-300 border border-dark-600 shrink-0">TV
                                                        Series</span>
                                                </template>
                                                <template x-if="item.media_type === 'movie'">
                                                    <span
                                                        class="px-1.5 py-0.5 rounded bg-sky-600/20 text-[10px] font-bold text-sky-400 border border-sky-600/30 shrink-0">Movie</span>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                {{-- Thể loại --}}
                                <td class="px-5 py-3 text-dark-400">
                                    <span class="truncate block max-w-[200px]">
                                        <span x-text="item.genres.join(', ')"></span>
                                        <template x-if="item.genres_extra > 0">
                                            <span class="text-xs text-dark-500 ml-1 font-semibold"
                                                x-text="'(+' + item.genres_extra + ')'"></span>
                                        </template>
                                    </span>
                                </td>
                                {{-- Thao tác --}}
                                <td class="px-5 py-3 text-right space-x-1">
                                    {{-- Up --}}
                                    <button @click="move(item, 'up')" :disabled="index === 0 || moving"
                                        class="p-1.5 text-dark-400 hover:text-white rounded disabled:opacity-30 disabled:cursor-not-allowed transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 15l7-7 7 7" />
                                        </svg>
                                    </button>
                                    {{-- Down --}}
                                    <button @click="move(item, 'down')" :disabled="index === items.length - 1 || moving"
                                        class="p-1.5 text-dark-400 hover:text-white rounded disabled:opacity-30 disabled:cursor-not-allowed transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    {{-- Remove --}}
                                    <form :id="'remove-' + item.type + '-' + item.id" :action="item.destroy_url"
                                        method="POST" class="inline-block ml-2">
                                        @csrf
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="button"
                                            class="p-1.5 text-red-500 hover:text-red-400 rounded transition-colors"
                                            title="Gỡ khỏi Carousel"
                                            @click="$dispatch('admin-confirm', { title: 'Gỡ khỏi Carousel', message: 'Gỡ «' + item.title + '» khỏi Carousel?', formId: 'remove-' + item.type + '-' + item.id })">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>


        {{-- MODAL CHỌN PHIM --}}
        <div x-show="addModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            {{-- Backdrop --}}
            <div x-show="addModalOpen" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-dark-950/80 backdrop-blur-sm transition-opacity" aria-hidden="true"
                @click="addModalOpen = false"></div>

            <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
                <div x-show="addModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative bg-dark-900 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-2xl w-full border border-dark-700 flex flex-col max-h-[85vh]">

                    {{-- Modal Header --}}
                    <div class="px-6 py-5 border-b border-dark-800 flex items-center justify-between shrink-0">
                        <div>
                            <h3 class="text-xl font-bold text-white" id="modal-title">Chọn phim/series ghim lên
                                Carousel</h3>
                            <p class="text-sm text-dark-400 mt-1">Danh sách dưới đây là các phim và series thỏa mãn
                                điều kiện có đầy đủ Ảnh, Nền và Trailer.</p>
                        </div>
                        <button type="button" class="text-dark-400 hover:text-white" @click="addModalOpen = false">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Modal Body - Search & List --}}
                    <div class="p-6 flex-1 overflow-hidden flex flex-col">
                        <input type="text" x-model="search" placeholder="Tìm tên phim hoặc series..."
                            class="input-dark w-full mb-4 shrink-0 focus:!border-sky-500 focus:!ring-sky-500">

                        <div class="flex-1 overflow-y-auto pr-2 space-y-2 relative" style="min-h: 300px;">
                            @foreach ($eligibleMovies as $movie)
                                <div class="flex items-center justify-between p-3 rounded-xl bg-dark-800 border border-dark-700/50 hover:border-dark-600 transition-colors"
                                    x-show="search === '' || '{{ strtolower(addslashes($movie->title)) }}'.includes(search.toLowerCase())">
                                    <div class="flex items-center gap-4 min-w-0">
                                        <div class="w-12 h-16 rounded overflow-hidden bg-dark-900 shrink-0">
                                            <img src="{{ $movie->poster }}" class="w-full h-full object-cover">
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2">
                                                <p class="font-semibold text-white truncate">{{ $movie->title }}</p>
                                                @if ($movie->media_type === 'tv')
                                                    <span
                                                        class="px-1.5 py-0.5 rounded bg-dark-700 text-[10px] font-bold text-dark-300 border border-dark-600 shrink-0">TV</span>
                                                @else
                                                    <span
                                                        class="px-1.5 py-0.5 rounded bg-sky-600/20 text-[10px] font-bold text-sky-400 border border-sky-600/30 shrink-0">Movie</span>
                                                @endif
                                            </div>
                                            @php
                                                $genres = $movie->genres->pluck('name');
                                                $displayGenres = $genres->take(2)->implode(', ');
                                                $remaining = $genres->count() - 2;
                                            @endphp
                                            <p class="text-xs text-dark-400 truncate mt-0.5">
                                                {{ $displayGenres }}
                                                @if ($remaining > 0)
                                                    <span class="ml-1 font-semibold">(+{{ $remaining }})</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <form action="{{ route('admin.carousel.store') }}" method="POST"
                                        class="shrink-0 ml-4">
                                        @csrf
                                        <input type="hidden" name="media_id" value="{{ $movie->id }}">
                                        <input type="hidden" name="media_type" value="{{ $movie->media_type }}">
                                        @if ($movie->is_featured)
                                            <span
                                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-dark-700 text-xs font-semibold text-dark-400 border border-dark-600 cursor-default">
                                                <svg class="w-3.5 h-3.5 text-green-500" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Đã ghim
                                            </span>
                                        @else
                                            <button type="submit" class="btn-sky text-xs px-3 py-1.5 rounded-lg">Chọn
                                                lên Carousel</button>
                                        @endif
                                    </form>
                                </div>
                            @endforeach

                            @if ($eligibleMovies->isEmpty())
                                <div class="text-center py-12 text-dark-400">
                                    <p>Không có phim hoặc series nào đủ điều kiện hoặc chưa ghim.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Criteria note --}}
    <p class="text-xs text-dark-400 mt-5 leading-relaxed max-w-5xl">
        Tự động cập nhật lấy
        <span class="font-semibold text-dark-300">Top 5 Phim và Top 5 TV Series</span>
        có lượt xem
        <span class="font-semibold text-dark-300">cao nhất</span>,
        yêu cầu phải có đủ
        <span class="font-semibold text-dark-300">Backdrop · Poster · Trailer</span>.
        Thiếu một trong ba sẽ bị
        <span class="font-semibold text-dark-300">loại bỏ</span>
        khỏi danh sách ứng viên.
    </p>

    @php
        $carouselInitialData = $featuredMovies
            ->map(function ($m) {
                return [
                    'id' => $m->id,
                    'type' => $m->media_type,
                    'title' => $m->title,
                    'poster' => $m->poster,
                    'media_type' => $m->media_type,
                    'order' => $m->featured_order,
                    'genres' => $m->genres->pluck('name')->take(2)->values(),
                    'genres_extra' => max(0, $m->genres->count() - 2),
                    'move_url' => route('admin.carousel.moveAjax', ['type' => $m->media_type, 'id' => $m->id]),
                    'destroy_url' => route('admin.carousel.destroy', ['type' => $m->media_type, 'id' => $m->id]),
                ];
            })
            ->values();
    @endphp

    <script>
        const __CAROUSEL_INITIAL__ = {!! json_encode($carouselInitialData) !!};

        function carouselManager() {
            return {
                items: __CAROUSEL_INITIAL__,
                addModalOpen: false,
                search: '',
                moving: false,
                movingId: null,

                async move(item, direction) {
                    if (this.moving) return;
                    this.moving = true;
                    this.movingId = item.type + '-' + item.id;

                    try {
                        const res = await fetch(item.move_url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                direction
                            }),
                        });

                        const data = await res.json();
                        if (data.ok) {
                            // Cập nhật danh sách mà không reload
                            this.items = data.items;
                        }
                    } catch (e) {
                        console.error('Carousel move failed', e);
                    } finally {
                        this.moving = false;
                        this.movingId = null;
                    }
                }
            };
        }
    </script>

</x-admin-layout>
