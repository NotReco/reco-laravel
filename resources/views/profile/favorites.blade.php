<x-app-layout>
    <div class="min-h-screen py-12 relative overflow-hidden">
        {{-- Overall Page Background Blobs --}}
        <div class="fixed top-0 left-0 w-full h-full pointer-events-none -z-10">
            <div class="absolute top-1/4 -left-1/4 w-[800px] h-[800px] bg-sky-200/20 rounded-full blur-[120px]"></div>
            <div class="absolute bottom-0 right-0 w-[600px] h-[600px] bg-rose-200/20 rounded-full blur-[120px]"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header Card --}}
            <div class="bg-white/80 backdrop-blur-xl rounded-3xl border border-white/60 shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-6 md:p-8 mb-10 relative overflow-hidden">
                {{-- Decorative background --}}
                <div class="absolute -top-24 -right-24 w-[400px] h-[400px] bg-gradient-to-bl from-rose-300/30 to-pink-300/10 rounded-full blur-[60px] pointer-events-none"></div>
                
                <div class="relative z-10 flex flex-col sm:flex-row sm:items-start gap-4 md:gap-6">
                    <a href="{{ route('profile.show', $user) }}" class="shrink-0 w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-gray-200/80 text-gray-500 hover:text-gray-900 hover:bg-gray-50 hover:shadow-md transition-all shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </a>
                    
                    <div>
                        <div class="flex items-center gap-2.5 mb-2">
                            <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-rose-500 to-pink-600 flex items-center justify-center shadow-lg shadow-rose-500/30">
                                <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                            </div>
                            <h1 class="text-2xl md:text-3xl font-display font-bold text-gray-900 tracking-tight">Phim Yêu Thích</h1>
                        </div>
                        <p class="text-gray-500 text-sm md:text-base ml-9.5">Bộ sưu tập của <a href="{{ route('profile.show', $user) }}" class="text-rose-600 hover:text-rose-700 hover:underline font-semibold transition-colors">{{ $user->name }}</a> &middot; <span class="font-medium text-gray-700">{{ $favorites->total() }}</span> tác phẩm</p>
                    </div>
                </div>
            </div>

            {{-- Grid --}}
            @if($favorites->isEmpty())
                <div class="bg-white/80 backdrop-blur-xl rounded-3xl p-16 text-center border border-white/60 shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative overflow-hidden">
                    <div class="w-20 h-20 bg-gradient-to-br from-rose-50 to-pink-50 rounded-2xl flex items-center justify-center mx-auto mb-6 border border-rose-100 shadow-inner">
                        <svg class="w-10 h-10 text-rose-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    </div>
                    @if($isOwnProfile)
                        <h3 class="text-xl font-display font-bold text-gray-900 mb-2">Bạn chưa yêu thích bộ phim nào</h3>
                        <p class="text-gray-500 text-sm md:text-base max-w-md mx-auto mb-8 leading-relaxed">Hãy theo dõi và khám phá thế giới điện ảnh rộng lớn, nhấn biểu tượng trái tim để lưu lại những tuyệt tác vào thư viện riêng của bạn nhé.</p>
                        <a href="{{ route('explore') }}" class="inline-flex items-center justify-center px-8 py-3.5 font-bold text-sm bg-gray-900 text-white rounded-xl hover:bg-gray-800 transition-colors shadow-lg shadow-gray-900/20">
                            Bắt đầu khám phá
                        </a>
                    @else
                        <h3 class="text-xl font-display font-bold text-gray-900 mb-2">Kho phim trống</h3>
                        <p class="text-gray-500 text-sm md:text-base"><span class="font-semibold text-gray-700">{{ $user->name }}</span> hiện chưa có bộ phim yêu thích nào.</p>
                    @endif
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-5 md:gap-6">
                    @foreach($favorites as $movie)
                        <a href="{{ route('movies.show', $movie) }}" class="group relative aspect-[2/3] rounded-2xl overflow-hidden bg-white border border-gray-200/60 shadow-[0_4px_12px_rgba(0,0,0,0.04)] block hover:shadow-[0_12px_24px_rgba(0,0,0,0.12)] hover:-translate-y-1.5 transition-all duration-300">
                            <img src="{{ $movie->poster }}" alt="{{ $movie->title }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" loading="lazy">
                            <div class="absolute inset-0 bg-gradient-to-t from-gray-900/90 via-gray-900/20 to-transparent opacity-80 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <div class="absolute bottom-0 left-0 right-0 p-4 opacity-90 transition-all duration-300">
                                <p class="text-white font-bold text-sm leading-snug line-clamp-2 drop-shadow-md">{{ $movie->title }}</p>
                                @if($movie->release_date)
                                    <p class="text-rose-200 font-medium text-xs mt-1">{{ \Carbon\Carbon::parse($movie->release_date)->format('Y') }}</p>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($favorites->hasPages())
                    <div class="mt-12 bg-white/60 backdrop-blur-sm rounded-2xl p-4 border border-gray-100">
                        {{ $favorites->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>
