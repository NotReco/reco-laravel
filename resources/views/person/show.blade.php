<x-app-layout>
    <x-slot:title>{{ $person->name }}</x-slot:title>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex flex-col md:flex-row gap-8 lg:gap-12">

            {{-- Left column: Photo & Info --}}
            <div class="w-full md:w-1/3 lg:w-1/4 shrink-0 space-y-6">
                <div class="w-48 h-48 md:w-full md:aspect-[3/4] mx-auto md:h-auto rounded-3xl overflow-hidden shadow-2xl border-4 border-dark-800 bg-dark-800 flex items-center justify-center">
                    @if($person->photo)
                        <img src="{{ $person->photo }}" alt="{{ $person->name }}" class="w-full h-full object-cover">
                    @else
                        <svg class="w-24 h-24 text-dark-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    @endif
                </div>

                {{-- Biography --}}
                @if($person->biography)
                    <div class="card p-6">
                        <h3 class="font-display font-bold text-white mb-3 flex items-center gap-2">
                            <svg class="w-5 h-5 text-accent-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                            Tiểu sử
                        </h3>
                        <x-expandable-text :text="$person->biography" :maxLength="250" />
                    </div>
                @endif
            </div>

            {{-- Right column: Movies --}}
            <div class="flex-1 space-y-12">
                {{-- Header --}}
                <div>
                    <h1 class="text-4xl md:text-5xl font-display font-bold text-white">{{ $person->name }}</h1>
                    <p class="text-dark-400 mt-2 text-lg">
                        Biết đến qua:
                        <span class="text-white font-medium">
                            {{ collect([
                                $actedMovies->count() > 0 ? 'Diễn xuất' : null,
                                $crewedByJob->has('Director') ? 'Đạo diễn' : null,
                                $crewedByJob->has('Writer') ? 'Biên kịch' : null,
                            ])->filter()->join(', ') }}
                        </span>
                    </p>
                </div>

                {{-- Acted Movies Grid --}}
                @if($actedMovies->isNotEmpty())
                    <section>
                        <h2 class="section-title mb-6">🎭 Phim đã tham gia ({{ $actedMovies->count() }})</h2>
                        <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                            @foreach($actedMovies as $movie)
                                <a href="{{ route('movies.show', $movie) }}" class="card-hover overflow-hidden rounded-xl bg-dark-800 flex gap-3 h-32 group">
                                    <div class="w-20 shrink-0 h-full">
                                        @if($movie->poster)
                                            <img src="{{ $movie->poster }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full bg-dark-700"></div>
                                        @endif
                                    </div>
                                    <div class="py-2.5 pr-2.5 flex-1 min-w-0 flex flex-col justify-center">
                                        <h4 class="text-sm font-semibold text-white group-hover:text-rose-400 transition-colors line-clamp-2 leading-tight mb-1">
                                            {{ $movie->title }}
                                        </h4>
                                        <p class="text-xs text-dark-400 mb-1">{{ $movie->release_date ? $movie->release_date->format('Y') : 'Chưa rõ' }}</p>
                                        @if($movie->pivot->character_name)
                                            <p class="text-xs font-medium text-rose-300 line-clamp-1">vai {{ $movie->pivot->character_name }}</p>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- Crew Roles Listing --}}
                @foreach($crewedByJob as $job => $movies)
                    <section>
                        <h2 class="section-title mb-6">🎬 Vai trò: {{ __($job) }} ({{ $movies->count() }})</h2>
                        <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                            @foreach($movies as $movie)
                                <a href="{{ route('movies.show', $movie) }}" class="card-hover overflow-hidden rounded-xl bg-dark-800 flex gap-3 h-32 group">
                                    <div class="w-20 shrink-0 h-full">
                                        @if($movie->poster)
                                            <img src="{{ $movie->poster }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full bg-dark-700"></div>
                                        @endif
                                    </div>
                                    <div class="py-2.5 pr-2.5 flex-1 min-w-0 flex flex-col justify-center">
                                        <h4 class="text-sm font-semibold text-white group-hover:text-rose-400 transition-colors line-clamp-2 leading-tight mb-1">
                                            {{ $movie->title }}
                                        </h4>
                                        <p class="text-xs text-dark-400 mb-1">{{ $movie->release_date ? $movie->release_date->format('Y') : 'Chưa rõ' }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>
