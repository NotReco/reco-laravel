<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Profile Header Card --}}
            <div class="card p-6 md:p-10 mb-8 relative overflow-hidden">
                {{-- Background decorative elements --}}
                <div class="absolute top-0 right-0 w-64 h-64 bg-sky-500/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
                
                <div class="relative z-10 flex flex-col md:flex-row items-center md:items-start gap-6 md:gap-10">
                    {{-- Avatar --}}
                    <div class="w-32 h-32 md:w-40 md:h-40 shrink-0 rounded-full border-4 border-dark-700 bg-gradient-to-br from-sky-500 to-sky-700 flex items-center justify-center overflow-hidden shadow-2xl">
                        @if($user->avatar)
                            <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-4xl md:text-5xl font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        @endif
                    </div>

                    {{-- Info & Stats --}}
                    <div class="flex-1 text-center md:text-left">
                        <div class="flex flex-col md:flex-row md:items-center gap-4 mb-4 justify-between">
                            <div>
                                <h1 class="text-3xl font-display font-bold text-white mb-1">{{ $user->name }}</h1>
                                <p class="text-dark-400 text-sm">Thành viên từ {{ $user->created_at->format('M Y') }}</p>
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
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-8">
                            <div class="bg-dark-900/50 rounded-xl p-4 border border-dark-700/50">
                                <div class="text-2xl font-display font-bold text-white mb-1">{{ $stats['reviews_count'] }}</div>
                                <div class="text-xs font-medium text-dark-400 uppercase tracking-wider">Review</div>
                            </div>
                            <div class="bg-dark-900/50 rounded-xl p-4 border border-dark-700/50">
                                <div class="text-2xl font-display font-bold text-white mb-1">{{ $stats['favorites_count'] }}</div>
                                <div class="text-xs font-medium text-dark-400 uppercase tracking-wider">Phim Yêu Thích</div>
                            </div>
                            <div class="bg-dark-900/50 rounded-xl p-4 border border-dark-700/50">
                                <div class="text-2xl font-display font-bold text-white mb-1" id="display_followers_count">{{ $stats['followers_count'] }}</div>
                                <div class="text-xs font-medium text-dark-400 uppercase tracking-wider">Người theo dõi</div>
                            </div>
                            <div class="bg-dark-900/50 rounded-xl p-4 border border-dark-700/50">
                                <div class="text-2xl font-display font-bold text-white mb-1">{{ $stats['following_count'] }}</div>
                                <div class="text-xs font-medium text-dark-400 uppercase tracking-wider">Đang theo dõi</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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
