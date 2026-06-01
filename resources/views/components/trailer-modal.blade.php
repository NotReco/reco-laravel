{{-- Trailer Modal Component --}}
{{-- Used inside hero-carousel or movie detail page --}}
{{-- Requires Alpine.js parent with showTrailer, trailerUrl, closeTrailer() --}}

<div x-show="showTrailer" x-cloak style="display: none"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-[60] bg-black/90 flex items-center justify-center p-2 sm:p-4"
    @click.self="closeTrailer()"
>
    {{-- Close Button --}}
    <button @click="closeTrailer()"
        class="absolute top-2 right-2 md:top-4 md:right-4 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors z-10">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>

    {{-- YouTube IFrame with error fallback --}}
    <div class="w-full max-w-4xl aspect-video rounded-2xl overflow-hidden shadow-2xl relative bg-black flex flex-col justify-center">

        {{-- IFrame --}}
        <iframe x-show="!trailerError && showTrailer" :src="trailerUrl"
            class="w-full h-full" frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen
            @load="/* ok */"
            x-on:error="trailerError = true">
        </iframe>

        {{-- Fallback when embed fails --}}
        <div x-show="trailerError" style="display:none;"
             class="absolute inset-0 flex flex-col items-center justify-center gap-4 bg-gray-900 text-white text-center p-6 rounded-2xl z-10">
            <svg class="w-14 h-14 text-gray-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M15 10l4.553-2.276A1 1 0 0121 8.723v6.554a1 1 0 01-1.447.894L15 14M3 8a1 1 0 011-1h8a1 1 0 011 1v8a1 1 0 01-1 1H4a1 1 0 01-1-1V8z"/>
            </svg>
            <p class="text-gray-300 text-sm font-medium">Trailer này không hỗ trợ phát nhúng. Bạn có thể xem trực tiếp trên YouTube.</p>
            <div class="flex items-center gap-3">
                <a :href="trailerUrl.replace('/embed/', '/watch?v=').replace('?autoplay=1&rel=0', '')"
                   target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-colors shadow-lg">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19.59 6.69a4.83 4.83 0 01-3.77-2.75 12 12 0 00-10.54 0A4.83 4.83 0 011.5 6.69 44.32 44.32 0 000 12a44.32 44.32 0 001.5 5.31 4.83 4.83 0 003.78 2.75 12 12 0 0010.44 0 4.83 4.83 0 003.77-2.75A44.32 44.32 0 0024 12a44.32 44.32 0 00-4.41-5.31zM9.75 15.02V8.98L15.5 12l-5.75 3.02z"/>
                    </svg>
                    Mở trên YouTube
                </a>
            </div>
        </div>
        
        {{-- Manual Actions (Floating at bottom) --}}
        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 z-20 flex gap-3" x-show="showTrailer">
            <template x-if="typeof trailerCandidates !== 'undefined' && trailerCandidates.length > (typeof currentTrailerIndex !== 'undefined' ? currentTrailerIndex : 0) + 1">
                <button @click="nextTrailer()"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-black/60 hover:bg-black/80 backdrop-blur text-white text-sm font-semibold rounded-full border border-white/20 transition-colors shadow-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    Video lỗi? Thử trailer khác
                </button>
            </template>
            <template x-if="typeof trailerCandidates === 'undefined' || trailerCandidates.length <= (typeof currentTrailerIndex !== 'undefined' ? currentTrailerIndex : 0) + 1">
                <a :href="trailerUrl ? trailerUrl.replace('/embed/', '/watch?v=').replace('?autoplay=1&rel=0', '') : '#'"
                   target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-black/60 hover:bg-black/80 backdrop-blur text-white text-sm font-semibold rounded-full border border-white/20 transition-colors shadow-lg">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19.59 6.69a4.83 4.83 0 01-3.77-2.75 12 12 0 00-10.54 0A4.83 4.83 0 011.5 6.69 44.32 44.32 0 000 12a44.32 44.32 0 001.5 5.31 4.83 4.83 0 003.78 2.75 12 12 0 0010.44 0 4.83 4.83 0 003.77-2.75A44.32 44.32 0 0024 12a44.32 44.32 0 00-4.41-5.31zM9.75 15.02V8.98L15.5 12l-5.75 3.02z"/>
                    </svg>
                    Video bị chặn? Xem trên YouTube
                </a>
            </template>
        </div>
    </div>
</div>
