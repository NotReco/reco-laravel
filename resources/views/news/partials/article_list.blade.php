@if ($articles->isEmpty())
    <div class="text-center py-20">
        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
        </svg>
        <p class="text-gray-400 text-lg">Chưa có tin tức nào.</p>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($articles as $article)
            <a href="{{ route('news.show', $article) }}"
                class="group bg-white rounded-2xl border border-gray-200 overflow-hidden hover:shadow-lg hover:shadow-gray-200/60 hover:border-gray-300 transition-all duration-300">

                {{-- Thumbnail --}}
                <div class="aspect-[16/9] bg-gradient-to-br from-gray-100 to-gray-200 overflow-hidden">
                    @if ($article->thumbnail)
                        <img src="{{ $article->thumbnail }}" alt="{{ $article->title }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                            loading="lazy">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                            </svg>
                        </div>
                    @endif
                </div>

                {{-- Content --}}
                <div class="p-5">
                    {{-- Tags --}}
                    @if ($article->tags->isNotEmpty())
                        <div class="flex flex-wrap gap-1.5 mb-3">
                            @foreach ($article->tags->take(2) as $tag)
                                <span
                                    class="px-2 py-0.5 bg-gray-100 text-gray-600 text-[11px] font-semibold rounded-md uppercase tracking-wide">
                                    {{ $tag->name }}
                                </span>
                            @endforeach
                            @if ($article->tags->count() > 2)
                                <span class="px-2 py-0.5 bg-gray-50 text-gray-400 text-[11px] font-semibold rounded-md">
                                    +{{ $article->tags->count() - 2 }}
                                </span>
                            @endif
                        </div>
                    @endif

                    {{-- Title --}}
                    <h2
                        class="text-lg font-bold text-gray-900 line-clamp-2 group-hover:text-rose-600 transition-colors leading-snug">
                        {{ $article->title }}
                    </h2>

                    {{-- Subtitle --}}
                    @if ($article->subtitle)
                        <p class="mt-1.5 text-sm text-gray-500 line-clamp-2">{{ $article->subtitle }}</p>
                    @endif

                    {{-- Meta --}}
                    <div class="mt-4 flex items-center gap-2 text-xs text-gray-400">
                        <span class="font-medium text-gray-600">{{ $article->user->name ?? 'Ẩn danh' }}</span>
                        <span>-</span>
                        <span>{{ $article->published_at?->format('d/m/Y') }}</span>
                        <span>-</span>
                        <span>{{ $article->published_at?->format('H:i') }}</span>
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
