@if($articles->isEmpty())
    <div class="text-center py-20">
        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
        </svg>
        <p class="text-gray-400 text-lg">Chưa có tin tức nào.</p>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($articles as $article)
            <a href="{{ route('news.show', $article) }}"
               class="group bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-lg hover:shadow-gray-200/60 hover:border-gray-200 transition-all duration-300">

                {{-- Thumbnail --}}
                <div class="aspect-[16/9] bg-gradient-to-br from-gray-100 to-gray-200 overflow-hidden">
                    @if($article->thumbnail)
                        <img src="{{ $article->thumbnail }}" alt="{{ $article->title }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                            </svg>
                        </div>
                    @endif
                </div>

                {{-- Content --}}
                <div class="p-5">
                    {{-- Tags --}}
                    @if($article->tags->isNotEmpty())
                        <div class="flex flex-wrap gap-1.5 mb-3">
                            @foreach($article->tags->take(3) as $tag)
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-[11px] font-semibold rounded-md uppercase tracking-wide">
                                    {{ $tag->name }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    {{-- Title --}}
                    <h2 class="text-lg font-bold text-gray-900 line-clamp-2 group-hover:text-rose-600 transition-colors leading-snug">
                        {{ $article->title }}
                    </h2>

                    {{-- Subtitle --}}
                    @if($article->subtitle)
                        <p class="mt-1.5 text-sm text-gray-500 line-clamp-2">{{ $article->subtitle }}</p>
                    @endif

                    {{-- Meta --}}
                    <div class="mt-4 flex items-center gap-3 text-xs text-gray-400">
                        <div class="flex items-center gap-1.5">
                            <div class="w-5 h-5 rounded-full bg-gradient-to-br from-rose-400 to-rose-600 flex items-center justify-center shrink-0">
                                <span class="text-[10px] font-bold text-white">{{ strtoupper(substr($article->user->name ?? '?', 0, 1)) }}</span>
                            </div>
                            <span class="font-medium text-gray-600">{{ $article->user->name ?? 'Ẩn danh' }}</span>
                        </div>
                        <span>·</span>
                        <span>{{ $article->published_at?->format('d/m/Y') }}</span>
                        <span>·</span>
                        <span class="flex items-center gap-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            {{ $article->comments_count }}
                        </span>
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    {{-- Pagination (with AJAX click handler class) --}}
    <div class="mt-10 ajax-pagination">
        {{ $articles->links() }}
    </div>
@endif
