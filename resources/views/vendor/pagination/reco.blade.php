@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-between items-center sm:justify-center">
        <!-- Responsive simple links for mobile -->
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-dark-600 bg-dark-900/50 border border-dark-800 cursor-default leading-5 rounded-xl">
                    &laquo; Trước
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-dark-300 bg-dark-800 border border-dark-700 leading-5 rounded-xl hover:text-white hover:bg-dark-700 hover:z-10 focus:outline-none transition ease-in-out duration-150">
                    &laquo; Trước
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-dark-300 bg-dark-800 border border-dark-700 leading-5 rounded-xl hover:text-white hover:bg-dark-700 hover:z-10 focus:outline-none transition ease-in-out duration-150">
                    Sau &raquo;
                </a>
            @else
                <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-dark-600 bg-dark-900/50 border border-dark-800 cursor-default leading-5 rounded-xl">
                    Sau &raquo;
                </span>
            @endif
        </div>

        <!-- Full pagination for desktop -->
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-center">
            <div>
                <span class="inline-flex gap-1.5">
                    {{-- First Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="First" class="relative flex items-center justify-center w-auto px-3 h-10 text-sm font-medium text-dark-600 bg-dark-900/50 border border-dark-800 cursor-default rounded-xl leading-5">
                            &lt;&lt;
                        </span>
                    @else
                        <a href="{{ $paginator->url(1) }}" rel="first" aria-label="First" class="relative flex items-center justify-center w-auto px-3 h-10 text-sm font-medium text-dark-300 bg-dark-800 border border-dark-700 rounded-xl leading-5 hover:text-sky-400 hover:bg-dark-700 hover:border-sky-500/50 hover:z-10 focus:z-10 focus:outline-none transition ease-in-out duration-200 shadow-sm">
                            &lt;&lt;
                        </a>
                    @endif

                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="Previous" class="relative flex items-center justify-center w-10 h-10 text-sm font-medium text-dark-600 bg-dark-900/50 border border-dark-800 cursor-default rounded-xl leading-5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous" class="relative flex items-center justify-center w-10 h-10 text-sm font-medium text-dark-300 bg-dark-800 border border-dark-700 rounded-xl leading-5 hover:text-sky-400 hover:bg-dark-700 hover:border-sky-500/50 hover:z-10 focus:z-10 focus:outline-none transition ease-in-out duration-200 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true" class="relative flex items-center justify-center w-10 h-10 text-sm font-medium text-dark-500 cursor-default leading-5">
                                {{ $element }}
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page" class="relative z-20 flex items-center justify-center w-10 h-10 text-sm font-bold text-white bg-sky-600 border border-sky-500 cursor-default leading-5 rounded-xl shadow-[0_0_15px_rgba(2,132,199,0.5)]">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}" aria-label="{{ __('Go to page :page', ['page' => $page]) }}" class="relative flex items-center justify-center w-10 h-10 text-sm font-medium text-dark-300 bg-dark-800 border border-dark-700 leading-5 rounded-xl hover:text-sky-400 hover:bg-dark-700 hover:border-sky-500/50 hover:z-10 focus:z-10 focus:outline-none transition ease-in-out duration-200 shadow-sm">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next" class="relative flex items-center justify-center w-10 h-10 text-sm font-medium text-dark-300 bg-dark-800 border border-dark-700 rounded-xl leading-5 hover:text-sky-400 hover:bg-dark-700 hover:border-sky-500/50 hover:z-10 focus:z-10 focus:outline-none transition ease-in-out duration-200 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                        {{-- Last Page Link --}}
                        <a href="{{ $paginator->url($paginator->lastPage()) }}" rel="last" aria-label="Last" class="relative flex items-center justify-center w-auto px-3 h-10 text-sm font-medium text-dark-300 bg-dark-800 border border-dark-700 rounded-xl leading-5 hover:text-sky-400 hover:bg-dark-700 hover:border-sky-500/50 hover:z-10 focus:z-10 focus:outline-none transition ease-in-out duration-200 shadow-sm">
                            &gt;&gt;
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="Next" class="relative flex items-center justify-center w-10 h-10 text-sm font-medium text-dark-600 bg-dark-900/50 border border-dark-800 cursor-default rounded-xl leading-5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </span>
                        {{-- Last Page Link --}}
                        <span aria-disabled="true" aria-label="Last" class="relative flex items-center justify-center w-auto px-3 h-10 text-sm font-medium text-dark-600 bg-dark-900/50 border border-dark-800 cursor-default rounded-xl leading-5">
                            &gt;&gt;
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
