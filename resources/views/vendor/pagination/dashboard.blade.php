@if ($paginator->hasPages())
<nav class="flex items-center justify-center gap-1 flex-wrap" aria-label="Pagination">

    {{-- << First --}}
    @if ($paginator->onFirstPage())
        <span class="flex items-center justify-center w-8 h-8 text-xs text-dark-600 bg-dark-900/30 border border-dark-800 rounded-lg cursor-default select-none">&laquo;</span>
    @else
        <a href="{{ $paginator->url(1) }}" class="flex items-center justify-center w-8 h-8 text-xs text-dark-400 bg-dark-800 border border-dark-700 rounded-lg hover:text-sky-400 hover:border-sky-500/50 transition-all duration-150">&laquo;</a>
    @endif

    {{-- < Prev --}}
    @if ($paginator->onFirstPage())
        <span class="flex items-center justify-center w-8 h-8 text-xs text-dark-600 bg-dark-900/30 border border-dark-800 rounded-lg cursor-default select-none">&lsaquo;</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="flex items-center justify-center w-8 h-8 text-xs text-dark-400 bg-dark-800 border border-dark-700 rounded-lg hover:text-sky-400 hover:border-sky-500/50 transition-all duration-150">&lsaquo;</a>
    @endif

    {{-- Page numbers --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="flex items-center justify-center w-8 h-8 text-xs text-dark-500 cursor-default select-none">…</span>
        @endif
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="flex items-center justify-center w-8 h-8 text-xs font-bold text-white bg-sky-600 border border-sky-500 rounded-lg shadow-[0_0_10px_rgba(2,132,199,0.4)] cursor-default select-none">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="flex items-center justify-center w-8 h-8 text-xs text-dark-300 bg-dark-800 border border-dark-700 rounded-lg hover:text-sky-400 hover:border-sky-500/50 transition-all duration-150">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- > Next --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="flex items-center justify-center w-8 h-8 text-xs text-dark-400 bg-dark-800 border border-dark-700 rounded-lg hover:text-sky-400 hover:border-sky-500/50 transition-all duration-150">&rsaquo;</a>
    @else
        <span class="flex items-center justify-center w-8 h-8 text-xs text-dark-600 bg-dark-900/30 border border-dark-800 rounded-lg cursor-default select-none">&rsaquo;</span>
    @endif

    {{-- >> Last --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->url($paginator->lastPage()) }}" class="flex items-center justify-center w-8 h-8 text-xs text-dark-400 bg-dark-800 border border-dark-700 rounded-lg hover:text-sky-400 hover:border-sky-500/50 transition-all duration-150">&raquo;</a>
    @else
        <span class="flex items-center justify-center w-8 h-8 text-xs text-dark-600 bg-dark-900/30 border border-dark-800 rounded-lg cursor-default select-none">&raquo;</span>
    @endif

</nav>
@endif
