@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}">

        <div class="flex gap-2 items-center justify-between sm:hidden">

            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-400 bg-white border border-slate-300 cursor-not-allowed leading-5 rounded-lg dark:text-zinc-600 dark:bg-zinc-950 dark:border-zinc-800">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-800 bg-white border border-slate-300 leading-5 rounded-lg hover:text-slate-900 focus:outline-none focus:ring-1 focus:ring-zinc-400 active:bg-slate-100 transition ease-in-out duration-150 dark:bg-zinc-900 dark:border-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-800 dark:hover:text-white">
                    {!! __('pagination.previous') !!}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-800 bg-white border border-slate-300 leading-5 rounded-lg hover:text-slate-900 focus:outline-none focus:ring-1 focus:ring-zinc-400 active:bg-slate-100 transition ease-in-out duration-150 dark:bg-zinc-900 dark:border-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-800 dark:hover:text-white">
                    {!! __('pagination.next') !!}
                </a>
            @else
                <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-400 bg-white border border-slate-300 cursor-not-allowed leading-5 rounded-lg dark:text-zinc-600 dark:bg-zinc-950 dark:border-zinc-800">
                    {!! __('pagination.next') !!}
                </span>
            @endif

        </div>

        <div class="hidden sm:flex-1 sm:flex sm:gap-2 sm:items-center sm:justify-between">

            <div>
                <p class="text-sm text-slate-600 leading-5 dark:text-zinc-400">
                    {!! __('Showing') !!}
                    @if ($paginator->firstItem())
                        <span class="font-medium text-slate-900 dark:text-zinc-200">{{ $paginator->firstItem() }}</span>
                        {!! __('to') !!}
                        <span class="font-medium text-slate-900 dark:text-zinc-200">{{ $paginator->lastItem() }}</span>
                    @else
                        {{ $paginator->count() }}
                    @endif
                    {!! __('of') !!}
                    <span class="font-medium text-slate-900 dark:text-zinc-200">{{ $paginator->total() }}</span>
                    {!! __('results') !!}
                </p>
            </div>

            <div>
                <span class="inline-flex rtl:flex-row-reverse shadow-xs rounded-lg">

                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                            <span class="inline-flex items-center px-2 py-2 text-sm font-medium text-slate-400 bg-white border border-slate-300 cursor-not-allowed rounded-l-lg leading-5 dark:bg-zinc-950 dark:border-zinc-800 dark:text-zinc-600" aria-hidden="true">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-2 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-300 rounded-l-lg leading-5 hover:text-slate-900 focus:outline-none focus:ring-1 focus:ring-zinc-400 active:bg-slate-100 transition ease-in-out duration-150 dark:bg-zinc-900 dark:border-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white" aria-label="{{ __('pagination.previous') }}">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-slate-500 bg-white border border-slate-300 cursor-default leading-5 dark:bg-zinc-900 dark:border-zinc-800 dark:text-zinc-500">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="inline-flex items-center px-4 py-2 -ml-px text-sm font-bold text-white bg-slate-900 border border-slate-900 cursor-default leading-5 dark:bg-zinc-100 dark:border-zinc-100 dark:text-zinc-950">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-slate-700 bg-white border border-slate-300 leading-5 hover:text-slate-900 hover:bg-slate-50 focus:outline-none focus:ring-1 focus:ring-zinc-400 active:bg-slate-100 transition ease-in-out duration-150 dark:bg-zinc-900 dark:border-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-slate-600 bg-white border border-slate-300 rounded-r-lg leading-5 hover:text-slate-900 focus:outline-none focus:ring-1 focus:ring-zinc-400 active:bg-slate-100 transition ease-in-out duration-150 dark:bg-zinc-900 dark:border-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white" aria-label="{{ __('pagination.next') }}">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                            <span class="inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-slate-400 bg-white border border-slate-300 cursor-not-allowed rounded-r-lg leading-5 dark:bg-zinc-950 dark:border-zinc-800 dark:text-zinc-600" aria-hidden="true">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
