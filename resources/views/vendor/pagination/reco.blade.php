@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-between items-center sm:justify-center">
        <!-- Responsive simple links for mobile -->
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span
                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-gray-50 border border-gray-200 cursor-default leading-5 rounded-xl">
                    &laquo; Trước
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 leading-5 rounded-xl hover:text-gray-900 hover:bg-gray-50 focus:outline-none transition ease-in-out duration-150">
                    &laquo; Trước
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                    class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-200 leading-5 rounded-xl hover:text-gray-900 hover:bg-gray-50 focus:outline-none transition ease-in-out duration-150">
                    Sau &raquo;
                </a>
            @else
                <span
                    class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-400 bg-gray-50 border border-gray-200 cursor-default leading-5 rounded-xl">
                    Sau &raquo;
                </span>
            @endif
        </div>

        <!-- Full pagination for desktop -->
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-center">
            <div>
                <span class="inline-flex gap-1">
                    {{-- First Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="First">
                            <span
                                class="relative flex items-center justify-center w-auto px-3 h-10 text-sm font-medium text-gray-400 bg-gray-50 border border-gray-200 cursor-default rounded-xl leading-5"
                                aria-hidden="true">
                                &lt;&lt;
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->url(1) }}" rel="first"
                            class="relative flex items-center justify-center w-auto px-3 h-10 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-xl leading-5 hover:text-sky-600 hover:bg-sky-50 hover:border-sky-200 focus:z-10 focus:outline-none transition ease-in-out duration-200 shadow-sm"
                            aria-label="First">
                            &lt;&lt;
                        </a>
                    @endif

                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="Previous">
                            <span
                                class="relative flex items-center justify-center w-10 h-10 text-sm font-medium text-gray-400 bg-gray-50 border border-gray-200 cursor-default rounded-xl leading-5"
                                aria-hidden="true">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                            class="relative flex items-center justify-center w-10 h-10 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-xl leading-5 hover:text-sky-600 hover:bg-sky-50 hover:border-sky-200 focus:z-10 focus:outline-none transition ease-in-out duration-200 shadow-sm"
                            aria-label="Previous">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span
                                    class="relative flex items-center justify-center w-10 h-10 text-sm font-medium text-gray-500 cursor-default leading-5">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span
                                            class="relative flex items-center justify-center w-10 h-10 text-sm font-bold text-white bg-sky-600 border border-sky-600 cursor-default leading-5 rounded-xl shadow-md shadow-sky-600/30">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}"
                                        class="relative flex items-center justify-center w-10 h-10 text-sm font-medium text-gray-700 bg-white border border-gray-200 leading-5 rounded-xl hover:text-sky-600 hover:bg-sky-50 hover:border-sky-200 focus:z-10 focus:outline-none transition ease-in-out duration-200 shadow-sm"
                                        aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                            class="relative flex items-center justify-center w-10 h-10 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-xl leading-5 hover:text-sky-600 hover:bg-sky-50 hover:border-sky-200 focus:z-10 focus:outline-none transition ease-in-out duration-200 shadow-sm"
                            aria-label="Next">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                        {{-- Last Page Link --}}
                        <a href="{{ $paginator->url($paginator->lastPage()) }}" rel="last"
                            class="relative flex items-center justify-center w-auto px-3 h-10 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-xl leading-5 hover:text-sky-600 hover:bg-sky-50 hover:border-sky-200 focus:z-10 focus:outline-none transition ease-in-out duration-200 shadow-sm"
                            aria-label="Last">
                            &gt;&gt;
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="Next">
                            <span
                                class="relative flex items-center justify-center w-10 h-10 text-sm font-medium text-gray-400 bg-gray-50 border border-gray-200 cursor-default rounded-xl leading-5"
                                aria-hidden="true">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </span>
                        </span>
                        {{-- Last Page Link --}}
                        <span aria-disabled="true" aria-label="Last">
                            <span
                                class="relative flex items-center justify-center w-auto px-3 h-10 text-sm font-medium text-gray-400 bg-gray-50 border border-gray-200 cursor-default rounded-xl leading-5"
                                aria-hidden="true">
                                &gt;&gt;
                            </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
