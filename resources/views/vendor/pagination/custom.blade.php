{{-- Premium pagination — shared by Dashboard and Courses Index so both
     pages render pixel-identical pagination controls. Registered as the
     app-wide default view in AppServiceProvider::boot(), so any ->links()
     call anywhere picks this up automatically; presentation only, the
     underlying LengthAwarePaginator logic is untouched. --}}
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex items-center justify-between gap-3">
        <div class="flex flex-1 justify-between sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="btn-secondary pointer-events-none opacity-40">
                    <x-icon name="chevron-left" class="h-4 w-4" />
                    {{ __('Previous') }}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="btn-secondary">
                    <x-icon name="chevron-left" class="h-4 w-4" />
                    {{ __('Previous') }}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="btn-secondary">
                    {{ __('Next') }}
                    <x-icon name="chevron-right" class="h-4 w-4" />
                </a>
            @else
                <span class="btn-secondary pointer-events-none opacity-40">
                    {{ __('Next') }}
                    <x-icon name="chevron-right" class="h-4 w-4" />
                </span>
            @endif
        </div>

        <div class="hidden w-full sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <p class="text-sm text-(--color-text-secondary)">
                {{ __('Showing') }}
                <span class="font-semibold text-(--color-text) dark:text-white">{{ $paginator->firstItem() }}</span>
                {{ __('to') }}
                <span class="font-semibold text-(--color-text) dark:text-white">{{ $paginator->lastItem() }}</span>
                {{ __('of') }}
                <span class="font-semibold text-(--color-text) dark:text-white">{{ $paginator->total() }}</span>
                {{ __('results') }}
            </p>

            <div class="flex items-center gap-1.5">
                @if ($paginator->onFirstPage())
                    <span class="pagination-btn pointer-events-none opacity-40" aria-disabled="true">
                        <x-icon name="chevron-left" class="h-4 w-4" />
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="pagination-btn" rel="prev" aria-label="Previous page">
                        <x-icon name="chevron-left" class="h-4 w-4" />
                    </a>
                @endif

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="pagination-btn pointer-events-none">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="pagination-btn pagination-btn-active">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="pagination-btn">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="pagination-btn" rel="next" aria-label="Next page">
                        <x-icon name="chevron-right" class="h-4 w-4" />
                    </a>
                @else
                    <span class="pagination-btn pointer-events-none opacity-40" aria-disabled="true">
                        <x-icon name="chevron-right" class="h-4 w-4" />
                    </span>
                @endif
            </div>
        </div>
    </nav>
@endif
