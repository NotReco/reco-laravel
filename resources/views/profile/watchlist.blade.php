<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3 mb-8">
                <svg class="w-8 h-8 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>
                <h1 class="text-3xl font-display font-bold text-white">Danh sách của tôi</h1>
            </div>

            @if($watchlists->isEmpty())
                <div class="card p-12 text-center">
                    <div class="w-20 h-20 bg-dark-800 rounded-full flex items-center justify-center mx-auto mb-4 border border-dark-700">
                        <svg class="w-10 h-10 text-dark-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Danh sách trống</h3>
                    <p class="text-dark-400 mb-6">Bạn chưa lưu bộ phim nào vào danh sách của mình.</p>
                    <a href="{{ route('explore') }}" class="btn-primary inline-flex">Khám phá phim ngay</a>
                </div>
            @else
                {{-- Tabs Component (Alpine.js) --}}
                <div x-data="{ activeTab: 'want_to_watch' }">
                    <div class="flex gap-2 overflow-x-auto pb-4 mb-6 scrollbar-hide">
                        <button @click="activeTab = 'want_to_watch'"
                                :class="activeTab === 'want_to_watch' ? 'bg-sky-600 text-white border-sky-500 shadow-lg shadow-sky-900/20' : 'bg-dark-800 text-dark-300 border-dark-700 hover:text-white hover:border-dark-500'"
                                class="px-5 py-2.5 rounded-xl border font-medium text-sm transition-all whitespace-nowrap flex items-center gap-2">
                            <span>Muốn xem</span>
                            <span class="bg-dark-950/50 text-xs py-0.5 px-2 rounded-full">{{ $watchlists->get('want_to_watch') ? $watchlists->get('want_to_watch')->count() : 0 }}</span>
                        </button>
                        <button @click="activeTab = 'watching'"
                                :class="activeTab === 'watching' ? 'bg-sky-600 text-white border-sky-500 shadow-lg shadow-sky-900/20' : 'bg-dark-800 text-dark-300 border-dark-700 hover:text-white hover:border-dark-500'"
                                class="px-5 py-2.5 rounded-xl border font-medium text-sm transition-all whitespace-nowrap flex items-center gap-2">
                            <span>Đang xem</span>
                            <span class="bg-dark-950/50 text-xs py-0.5 px-2 rounded-full">{{ $watchlists->get('watching') ? $watchlists->get('watching')->count() : 0 }}</span>
                        </button>
                        <button @click="activeTab = 'watched'"
                                :class="activeTab === 'watched' ? 'bg-sky-600 text-white border-sky-500 shadow-lg shadow-sky-900/20' : 'bg-dark-800 text-dark-300 border-dark-700 hover:text-white hover:border-dark-500'"
                                class="px-5 py-2.5 rounded-xl border font-medium text-sm transition-all whitespace-nowrap flex items-center gap-2">
                            <span>Đã xem</span>
                            <span class="bg-dark-950/50 text-xs py-0.5 px-2 rounded-full">{{ $watchlists->get('watched') ? $watchlists->get('watched')->count() : 0 }}</span>
                        </button>
                        <button @click="activeTab = 'dropped'"
                                :class="activeTab === 'dropped' ? 'bg-sky-600 text-white border-sky-500 shadow-lg shadow-sky-900/20' : 'bg-dark-800 text-dark-300 border-dark-700 hover:text-white hover:border-dark-500'"
                                class="px-5 py-2.5 rounded-xl border font-medium text-sm transition-all whitespace-nowrap flex items-center gap-2">
                            <span>Bỏ dở</span>
                            <span class="bg-dark-950/50 text-xs py-0.5 px-2 rounded-full">{{ $watchlists->get('dropped') ? $watchlists->get('dropped')->count() : 0 }}</span>
                        </button>
                    </div>

                    {{-- Tab Contents --}}
                    @foreach(['want_to_watch', 'watching', 'watched', 'dropped'] as $status)
                        <div x-show="activeTab === '{{ $status }}'" style="display: none;">
                            @if(isset($watchlists[$status]) && $watchlists[$status]->isNotEmpty())
                                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4 md:gap-6">
                                    @foreach($watchlists[$status] as $item)
                                        <div class="relative group">
                                            <x-movie-card :movie="$item->movie" />
                                            {{-- Remove button overlay --}}
                                            <form action="{{ route('watchlist.toggle') }}" method="POST" class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity z-10">
                                                @csrf
                                                <input type="hidden" name="movie_id" value="{{ $item->movie_id }}">
                                                <button type="submit" class="w-8 h-8 rounded-full bg-dark-900/80 backdrop-blur-sm border border-dark-600 text-dark-300 hover:text-sky-500 hover:border-sky-500/50 hover:bg-sky-500/10 flex items-center justify-center transition-all shadow-lg" title="Gỡ khỏi danh sách">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="card p-12 text-center border-dashed border-2 border-dark-700 bg-transparent">
                                    <p class="text-dark-400">Không có phim nào trong mục này.</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
