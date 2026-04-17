<x-app-layout>
    <div class="min-h-screen py-12 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header Card --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-[0_4px_20px_-2px_rgba(0,0,0,0.03)] p-6 md:p-8 mb-10 relative overflow-hidden">
                <div class="relative z-10 flex flex-col sm:flex-row sm:items-start gap-4 md:gap-6">
                    <a href="{{ route('profile.show', $user) }}" class="shrink-0 w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 border border-slate-200 text-slate-500 hover:text-slate-900 hover:bg-slate-100 hover:shadow-sm transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </a>
                    
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-pink-500 to-rose-600 shadow-sm flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" /></svg>
                            </div>
                            <h1 class="text-2xl md:text-3xl font-bold text-slate-900 tracking-tight">Phim Yêu Thích</h1>
                        </div>
                        <p class="text-slate-500 text-sm md:text-base ml-11">Bộ sưu tập của <a href="{{ route('profile.show', $user) }}" class="text-rose-600 hover:text-rose-700 hover:underline font-semibold transition-colors">{{ $user->name }}</a> &middot; <span class="font-medium text-slate-700">{{ $favorites->total() }}</span> tác phẩm</p>
                    </div>
                </div>
            </div>

            {{-- Grid --}}
            @if($favorites->isEmpty())
                <div class="border-2 border-dashed border-slate-200 bg-slate-50 rounded-2xl p-16 text-center transition-all hover:bg-slate-100/50 hover:border-slate-300">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-6 border border-slate-100 shadow-sm">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    </div>
                    @if($isOwnProfile)
                        <h3 class="text-lg font-bold text-slate-800 mb-2">Bạn chưa yêu thích bộ phim nào</h3>
                        <p class="text-slate-500 text-sm max-w-md mx-auto mb-8 leading-relaxed">Hãy theo dõi và khám phá thế giới điện ảnh rộng lớn, nhấn biểu tượng trái tim để lưu lại những tuyệt tác vào thư viện riêng của bạn nhé.</p>
                        <a href="{{ route('explore') }}" class="inline-flex items-center justify-center px-6 py-2.5 font-bold text-sm bg-slate-900 text-white rounded-lg hover:bg-slate-800 transition-colors shadow-sm">
                            Bắt đầu khám phá
                        </a>
                    @else
                        <h3 class="text-lg font-bold text-slate-800 mb-2">Kho phim trống</h3>
                        <p class="text-slate-500 text-sm"><span class="font-semibold text-slate-700">{{ $user->name }}</span> hiện chưa có bộ phim yêu thích nào.</p>
                    @endif
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-5 md:gap-6">
                    @foreach($favorites as $movie)
                        <a href="{{ route('movies.show', $movie) }}" class="group relative aspect-[2/3] rounded-xl overflow-hidden bg-white border border-slate-200 block hover:border-slate-300 hover:shadow-md transition-all duration-200">
                            <img src="{{ $movie->poster }}" alt="{{ $movie->title }}" class="w-full h-full object-cover" loading="lazy">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/85 via-slate-900/10 to-transparent"></div>
                            <div class="absolute bottom-0 left-0 right-0 p-3">
                                <p class="text-white font-semibold text-[11px] leading-tight line-clamp-2 drop-shadow-md">{{ $movie->title }}</p>
                                @if($movie->release_date)
                                    <p class="text-rose-200 font-medium text-[10px] mt-0.5">{{ \Carbon\Carbon::parse($movie->release_date)->format('Y') }}</p>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($favorites->hasPages())
                    <div class="mt-10 bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
                        {{ $favorites->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>
