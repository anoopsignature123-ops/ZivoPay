@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between pt-4 pb-2 px-2 w-full flex-col sm:flex-row gap-4">
        
        <!-- Left Side: Showing Results Info -->
        <div class="text-xs text-neutral-400 font-medium">
            @if ($paginator->firstItem())
                <span>Showing</span>
                <span class="font-bold text-emerald-400">{{ $paginator->firstItem() }}</span>
                <span>to</span>
                <span class="font-bold text-emerald-400">{{ $paginator->lastItem() }}</span>
                <span>of</span>
                <span class="font-bold text-emerald-400">{{ $paginator->total() }}</span>
                <span>results</span>
            @else
                {{ __('Showing') }} {{ $paginator->count() }} {{ __('results') }}
            @endif
        </div>

        <!-- Right Side: Horizontal Page Buttons Row -->
        <div class="inline-flex items-center gap-1.5 flex-wrap justify-center">
            
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                    <span class="px-3.5 py-1.5 rounded-lg bg-black/40 border border-neutral-800 text-neutral-600 font-bold text-xs inline-flex items-center gap-1 cursor-not-allowed select-none">
                        ‹ Prev
                    </span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('pagination.previous') }}" class="px-3.5 py-1.5 rounded-lg bg-black/80 border border-emerald-500/40 text-emerald-400 font-bold text-xs inline-flex items-center gap-1 hover:bg-emerald-500/20 hover:border-emerald-400 transition duration-200">
                    ‹ Prev
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span aria-disabled="true">
                        <span class="px-3 py-1.5 rounded-lg bg-black/40 border border-neutral-800 text-neutral-500 font-bold text-xs">
                            {{ $element }}
                        </span>
                    </span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page">
                                <span class="px-3.5 py-1.5 rounded-lg bg-emerald-500 text-black font-black text-xs shadow-[0_0_12px_rgba(16,185,129,0.4)]">
                                    {{ $page }}
                                </span>
                            </span>
                        @else
                            <a href="{{ $url }}" aria-label="{{ __('Go to page :page', ['page' => $page]) }}" class="px-3.5 py-1.5 rounded-lg bg-black/80 border border-emerald-500/30 text-emerald-400 font-bold text-xs hover:bg-emerald-500/20 hover:border-emerald-400 hover:text-emerald-300 transition duration-200">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('pagination.next') }}" class="px-3.5 py-1.5 rounded-lg bg-black/80 border border-emerald-500/40 text-emerald-400 font-bold text-xs inline-flex items-center gap-1 hover:bg-emerald-500/20 hover:border-emerald-400 transition duration-200">
                    Next ›
                </a>
            @else
                <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                    <span class="px-3.5 py-1.5 rounded-lg bg-black/40 border border-neutral-800 text-neutral-600 font-bold text-xs inline-flex items-center gap-1 cursor-not-allowed select-none">
                        Next ›
                    </span>
                </span>
            @endif

        </div>

    </nav>
@endif
