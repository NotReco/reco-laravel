@php
    $ratingBits = [];
    if (filled($article->rating_reco)) {
        $ratingBits[] = ['label' => 'Điểm ' . config('app.name', 'Reco'), 'value' => $article->rating_reco, 'accent' => true];
    }
    if (filled($article->rating_imdb)) {
        $ratingBits[] = ['label' => 'IMDb', 'value' => $article->rating_imdb, 'accent' => false];
    }
    if (filled($article->rating_metacritic)) {
        $ratingBits[] = ['label' => 'Metacritic', 'value' => $article->rating_metacritic, 'accent' => false];
    }
    if (filled($article->rating_rotten_tomatoes)) {
        $ratingBits[] = ['label' => 'Rotten Tomatoes', 'value' => $article->rating_rotten_tomatoes, 'accent' => false];
    }
    if (filled($article->rating_tmdb)) {
        $ratingBits[] = ['label' => 'TMDb', 'value' => $article->rating_tmdb, 'accent' => false];
    }
@endphp
@if (count($ratingBits) > 0)
    <div class="mt-2 flex flex-wrap items-baseline gap-x-2 gap-y-1 text-[13px] leading-snug text-gray-600">
        @foreach ($ratingBits as $i => $bit)
            @if ($i > 0)
                <span class="text-gray-300 select-none" aria-hidden="true">·</span>
            @endif
            <span class="whitespace-nowrap">
                <span @class([
                    'font-semibold',
                    'text-gray-700' => $bit['accent'],
                    'text-gray-600' => ! $bit['accent'],
                ])>{{ $bit['label'] }}</span>
                <span @class([
                    'ml-1 font-bold tabular-nums',
                    'text-rose-600 font-extrabold' => $bit['accent'],
                    'text-gray-900' => ! $bit['accent'],
                ])>{{ $bit['value'] }}</span>
            </span>
        @endforeach
    </div>
@endif
