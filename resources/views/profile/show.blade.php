<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Profile Header Card --}}
            <div class="card p-6 md:p-10 mb-8 relative overflow-hidden">
                {{-- Background decorative elements --}}
                <div class="absolute top-0 right-0 w-64 h-64 bg-sky-500/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
                
                <div class="relative z-10 flex flex-col md:flex-row items-center md:items-start gap-6 md:gap-10">
                    {{-- Avatar with Frame overlay --}}
                    <div class="relative w-32 h-32 md:w-40 md:h-40 shrink-0">
                        {{-- Avatar image --}}
                        <div class="w-full h-full rounded-full border-4 border-dark-700 bg-gradient-to-br from-sky-500 to-sky-700 flex items-center justify-center overflow-hidden shadow-2xl">
                            @if($user->avatar)
                                <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-4xl md:text-5xl font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            @endif
                        </div>
                        {{-- Frame overlay --}}
                        @if($user->activeFrame)
                            <img src="{{ Storage::url($user->activeFrame->image_path) }}" 
                                 alt="" 
                                 class="absolute inset-[-12%] w-[124%] h-[124%] object-contain pointer-events-none drop-shadow-lg z-10">
                        @endif
                    </div>

                    {{-- Info & Stats --}}
                    <div class="flex-1 text-center md:text-left">
                        <div class="flex flex-col md:flex-row md:items-center gap-4 mb-4 justify-between">
                            <div>
                                <div class="flex items-center justify-center md:justify-start gap-3 flex-wrap">
                                    <h1 class="text-3xl font-display font-bold text-white">{{ $user->name }}</h1>
                                    {{-- Active Title Badge --}}
                                    @if($user->activeTitle)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border shadow-sm"
                                              style="color: {{ $user->activeTitle->color_hex }}; border-color: {{ $user->activeTitle->color_hex }}40; background-color: {{ $user->activeTitle->color_hex }}15;">
                                            {{ $user->activeTitle->name }}
                                        </span>
                                    @endif
                                </div>
                                @php
                                    $months = ['01'=>'Tháng 1','02'=>'Tháng 2','03'=>'Tháng 3','04'=>'Tháng 4','05'=>'Tháng 5','06'=>'Tháng 6','07'=>'Tháng 7','08'=>'Tháng 8','09'=>'Tháng 9','10'=>'Tháng 10','11'=>'Tháng 11','12'=>'Tháng 12'];
                                @endphp
                                <p class="text-dark-200 text-sm mt-1">Thành viên từ {{ $months[$user->created_at->format('m')] }}, {{ $user->created_at->format('Y') }}</p>
                                {{-- Movie Quote --}}
                                @if($user->movie_quote)
                                    <div class="mt-3 max-w-lg pl-4 border-l-2 border-sky-500/40">
                                        <p class="text-dark-100/90 italic text-sm leading-relaxed">
                                            <svg class="inline-block w-3.5 h-3.5 text-sky-400/70 mr-0.5 -mt-1" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10H14.017zM0 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151C7.546 6.068 5.983 8.789 5.983 11h4v10H0z"/></svg>
                                            {{ $user->movie_quote }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                            
                            {{-- Interactive Buttons --}}
                            <div class="flex items-center justify-center md:justify-end gap-3 flex-wrap">
                                @if($isOwnProfile)
                                    <a href="{{ route('profile.edit') }}" class="btn-ghost text-sm">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        Chỉnh sửa
                                    </a>
                                @else
                                    {{-- Follow Toggle --}}
                                    <div x-data="{
                                        isFollowing: {{ $isFollowing ? 'true' : 'false' }},
                                        followersCount: {{ $stats['followers_count'] }},
                                        async toggleFollow() {
                                            @guest window.location.href = '{{ route('login') }}'; return; @endguest
                                            const res = await fetch('{{ route('follow.toggle') }}', {
                                                method: 'POST',
                                                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                                                body: JSON.stringify({ user_id: {{ $user->id }} })
                                            });
                                            const data = await res.json();
                                            if(data.success) {
                                                this.isFollowing = data.is_following;
                                                this.followersCount += this.isFollowing ? 1 : -1;
                                            } else if(data.message) {
                                                alert(data.message);
                                            }
                                        }
                                    }">
                                        <button @click="toggleFollow()" 
                                            :class="isFollowing ? 'btn-ghost' : 'btn-primary'" class="text-sm shadow-lg">
                                            <span x-text="isFollowing ? 'Đang theo dõi' : 'Tới theo dõi'"></span>
                                        </button>
                                        {{-- Update global text silently from Alpine if needed --}}
                                        <span class="hidden" x-effect="document.getElementById('display_followers_count').innerText = followersCount"></span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Stats Grid --}}
                        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mt-8">
                            <div class="bg-dark-900/50 rounded-xl p-4 border border-dark-700/50">
                                <div class="text-2xl font-display font-bold text-white mb-1">{{ $stats['reviews_count'] }}</div>
                                <div class="text-xs font-medium text-dark-200 uppercase tracking-wider">Review</div>
                            </div>
                            <div class="bg-dark-900/50 rounded-xl p-4 border border-dark-700/50">
                                <div class="text-2xl font-display font-bold text-white mb-1">{{ $stats['favorites_count'] }}</div>
                                <div class="text-xs font-medium text-dark-200 uppercase tracking-wider">Phim Yêu Thích</div>
                            </div>
                            <div class="bg-dark-900/50 rounded-xl p-4 border border-dark-700/50">
                                <div class="text-2xl font-display font-bold text-white mb-1" id="display_followers_count">{{ $stats['followers_count'] }}</div>
                                <div class="text-xs font-medium text-dark-200 uppercase tracking-wider">Người theo dõi</div>
                            </div>
                            <div class="bg-dark-900/50 rounded-xl p-4 border border-dark-700/50">
                                <div class="text-2xl font-display font-bold text-white mb-1">{{ $stats['following_count'] }}</div>
                                <div class="text-xs font-medium text-dark-200 uppercase tracking-wider">Đang theo dõi</div>
                            </div>
                            {{-- Reputation Score --}}
                            <div class="bg-gradient-to-br from-amber-500/10 to-orange-500/10 rounded-xl p-4 border border-amber-500/20">
                                <div class="text-2xl font-display font-bold text-amber-400 mb-1">{{ number_format($user->reputation_score) }}</div>
                                <div class="text-xs font-medium text-amber-500/70 uppercase tracking-wider flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    Uy tín
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Top 4 Phim Tâm Đắc --}}
            @if($user->topMovies->isNotEmpty())
                <div class="mb-8">
                    <h2 class="text-xl font-display font-bold text-white flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        Top Phim Tâm Đắc
                    </h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($user->topMovies as $index => $movie)
                            <a href="{{ route('movies.show', $movie) }}" class="group relative aspect-[2/3] rounded-xl overflow-hidden border border-dark-700/50 shadow-lg">
                                <img src="{{ $movie->poster }}" alt="{{ $movie->title }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy">
                                <div class="absolute inset-0 bg-gradient-to-t from-dark-950 via-transparent to-transparent"></div>
                                <div class="absolute bottom-0 left-0 right-0 p-3">
                                    <p class="text-white font-bold text-sm line-clamp-2 drop-shadow-lg">{{ $movie->title }}</p>
                                    @if($movie->release_date)
                                        <p class="text-dark-300 text-xs mt-0.5">{{ \Carbon\Carbon::parse($movie->release_date)->format('Y') }}</p>
                                    @endif
                                </div>
                                <div class="absolute top-2 left-2 w-7 h-7 bg-dark-950/80 backdrop-blur text-white text-xs font-bold rounded-lg flex items-center justify-center border border-dark-700/50">
                                    {{ $index + 1 }}
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Body Content: Favorites & Recent Reviews --}}
            <div class="grid lg:grid-cols-3 gap-8">
                
                {{-- Left sidebar: Favorites & Bio --}}
                <div class="lg:col-span-1 space-y-8">
                    @if($user->bio)
                    <div class="card p-6">
                        <h3 class="text-lg font-bold text-white mb-3">Giới thiệu</h3>
                        <p class="text-dark-300 text-sm leading-relaxed">{{ $user->bio }}</p>
                    </div>
                    @endif

                    <div class="card p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-white">Yêu thích gần đây</h3>
                            @if($isOwnProfile)
                                <a href="#" class="text-xs text-sky-500 hover:text-sky-400">Xem tất cả</a>
                            @endif
                        </div>
                        @if($user->favorites->isEmpty())
                            <div class="text-center py-8">
                                <p class="text-dark-400 text-sm">Chưa có phim yêu thích.</p>
                            </div>
                        @else
                            <div class="grid grid-cols-3 gap-2">
                                @foreach($user->favorites as $favMovie)
                                    <a href="{{ route('movies.show', $favMovie) }}" class="block shrink-0 group relative rounded-lg overflow-hidden aspect-[2/3]">
                                        <img src="{{ $favMovie->poster }}" alt="{{ $favMovie->title }}" class="w-full h-full object-cover transition-transform group-hover:scale-110" loading="lazy">
                                        <div class="absolute inset-0 bg-gradient-to-t from-dark-950/80 via-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-2">
                                            <span class="text-[10px] font-bold text-white leading-tight line-clamp-2">{{ $favMovie->title }}</span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Right Column: Recent Reviews --}}
                <div class="lg:col-span-2 space-y-6">
                    <h2 class="text-xl font-display font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        Review gần đây
                    </h2>

                    @forelse($user->reviews as $review)
                        <div class="card p-5 group flex flex-col md:flex-row gap-5">
                            @if($review->movie)
                                <a href="{{ route('movies.show', $review->movie) }}" class="shrink-0 w-24 md:w-32 aspect-[2/3] rounded-lg overflow-hidden shadow-lg border border-dark-700/50 block">
                                    <img src="{{ $review->movie->poster }}" alt="{{ $review->movie->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform" loading="lazy">
                                </a>
                            @endif
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-4 mb-2">
                                    <div>
                                        <h3 class="font-bold text-white text-lg line-clamp-1 mb-1">
                                            <a href="{{ route('movies.show', $review->movie) }}" class="hover:text-sky-400 transition-colors">{{ $review->title ?? 'Review phim ' . $review->movie->title }}</a>
                                        </h3>
                                        @if($review->rating)
                                            <x-star-rating :rating="$review->rating" />
                                        @endif
                                    </div>
                                    <span class="text-xs text-dark-400 whitespace-nowrap">{{ $review->created_at->diffForHumans() }}</span>
                                </div>
                                
                                @if($review->content)
                                    @if($review->is_spoiler)
                                        <x-spoiler-toggle>
                                            <div class="text-dark-300 text-sm leading-relaxed mb-4 line-clamp-3">
                                                {{ $review->content }}
                                            </div>
                                        </x-spoiler-toggle>
                                    @else
                                        <p class="text-dark-300 text-sm leading-relaxed mb-4 line-clamp-3">
                                            {{ $review->content }}
                                        </p>
                                    @endif
                                @endif
                                
                                <div class="flex items-center gap-4 border-t border-dark-700/50 pt-3 mt-auto">
                                    <span class="flex items-center text-xs font-medium text-dark-400">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/></svg>
                                        {{ $review->likes_count }} lượt thích
                                    </span>
                                    <span class="flex items-center text-xs font-medium text-dark-400">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                        {{ $review->comments_count }} bình luận
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="card p-12 text-center border-dashed border-2 border-dark-700 bg-transparent">
                            <div class="w-16 h-16 bg-dark-800 rounded-full flex items-center justify-center mx-auto mb-4 border border-dark-700">
                                <svg class="w-8 h-8 text-dark-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </div>
                            <p class="text-dark-400 text-sm">Người dùng này chưa viết review nào.</p>
                        </div>
                    @endforelse
                </div>
                
            </div>
        </div>
    </div>
</x-app-layout>
