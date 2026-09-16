@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between pt-4 pb-2 px-2 w-full">
        <div>
            @if ($paginator->onFirstPage())
                <span class="px-3.5 py-1.5 rounded-lg bg-black/40 border border-neutral-800 text-neutral-600 font-bold text-xs cursor-not-allowed select-none">
                    ‹ Previous
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="px-3.5 py-1.5 rounded-lg bg-black/80 border border-emerald-500/40 text-emerald-400 font-bold text-xs hover:bg-emerald-500/20 hover:border-emerald-400 transition duration-200">
                    ‹ Previous
                </a>
            @endif
        </div>

        <div>
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="px-3.5 py-1.5 rounded-lg bg-black/80 border border-emerald-500/40 text-emerald-400 font-bold text-xs hover:bg-emerald-500/20 hover:border-emerald-400 transition duration-200">
                    Next ›
                </a>
            @else
                <span class="px-3.5 py-1.5 rounded-lg bg-black/40 border border-neutral-800 text-neutral-600 font-bold text-xs cursor-not-allowed select-none">
                    Next ›
                </span>
            @endif
        </div>
    </nav>
@endif
