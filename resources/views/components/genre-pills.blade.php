{{-- Genre Pills Component --}}
{{-- Usage: <x-genre-pills :genres="$genres" :selectedId="request('genre')" /> --}}

@props(['genres', 'selectedId' => null])

<div class="flex items-center gap-3 flex-wrap">
    <a href="{{ route('explore') }}"
        class="{{ !$selectedId ? 'genre-pill-active' : 'genre-pill' }}">
        Tất cả
    </a>
    @foreach($genres as $genre)
        <a href="{{ route('explore', ['genre' => $genre->id]) }}"
            class="{{ $selectedId == $genre->id ? 'genre-pill-active' : 'genre-pill' }}">
            {{ $genre->name }}
            @if(isset($genre->movies_count))
                <span class="text-dark-500 ml-1">{{ $genre->movies_count }}</span>
            @endif
        </a>
    @endforeach
</div>
