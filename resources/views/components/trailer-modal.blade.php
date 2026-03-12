{{-- Trailer Modal Component --}}
{{-- Used inside hero-carousel or movie detail page --}}
{{-- Requires Alpine.js parent with showTrailer, trailerUrl, closeTrailer() --}}

<div x-show="showTrailer"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-[60] bg-black/90 flex items-center justify-center p-4"
    @click.self="closeTrailer()"
>
    {{-- Close Button --}}
    <button @click="closeTrailer()"
        class="absolute top-4 right-4 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors z-10">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>

    {{-- YouTube IFrame --}}
    <div class="w-full max-w-4xl aspect-video rounded-2xl overflow-hidden shadow-2xl">
        <iframe x-show="showTrailer" :src="trailerUrl"
            class="w-full h-full" frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen></iframe>
    </div>
</div>
