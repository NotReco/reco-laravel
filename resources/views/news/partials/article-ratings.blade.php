@if (
    $article->rating_reco ||
        $article->rating_imdb ||
        $article->rating_rotten_tomatoes ||
        $article->rating_metacritic ||
        $article->rating_tmdb)
    <div class="mt-3 flex items-center divide-x divide-gray-300/80 text-sm font-bold text-gray-700">
        @if ($article->rating_reco)
            <div class="flex items-center gap-1.5 px-3.5 first:pl-0 last:pr-0">
                <div class="flex items-center justify-center w-5 h-5 bg-black rounded-full p-[1px]">
                    <img src="{{ asset('storage/images/apple-touch-icon.png') }}" class="w-full h-full object-contain"
                        alt="Reco">
                </div>
                <span class="text-gray-900 font-extrabold">{{ $article->rating_reco }}</span>
            </div>
        @endif
        @if ($article->rating_imdb)
            <div class="flex items-center gap-1.5 px-3.5 first:pl-0 last:pr-0">
                <img src="{{ asset('storage/images/imdb.png') }}" class="h-5 w-auto object-contain rounded-sm"
                    alt="IMDb">
                <span class="text-gray-900">{{ $article->rating_imdb }}</span>
            </div>
        @endif
        @if ($article->rating_rotten_tomatoes)
            <div class="flex items-center gap-1.5 px-3.5 first:pl-0 last:pr-0">
                <img src="{{ asset('storage/images/tomato.png') }}" class="w-5 h-5 object-contain" alt="RT">
                <span class="text-gray-900">{{ $article->rating_rotten_tomatoes }}</span>
            </div>
        @endif
        @if ($article->rating_metacritic)
            <div class="flex items-center gap-1.5 px-3.5 first:pl-0 last:pr-0">
                <img src="{{ asset('storage/images/metacritic.png') }}" class="h-5 w-auto object-contain rounded-sm"
                    alt="MC">
                <span class="text-gray-900">{{ $article->rating_metacritic }}</span>
            </div>
        @endif
        @if ($article->rating_tmdb)
            <div class="flex items-center gap-1.5 px-3.5 first:pl-0 last:pr-0">
                <img src="{{ asset('storage/images/tmdb.png') }}" class="h-5 w-auto object-contain" alt="TMDb">
                <span class="text-gray-900">{{ $article->rating_tmdb }}</span>
            </div>
        @endif
    </div>
@endif
