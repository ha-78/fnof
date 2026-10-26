@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-muted">
            {!! __('Showing') !!}
            @if ($paginator->firstItem())
                <span class="font-medium text-ink">{{ $paginator->firstItem() }}</span>
                {!! __('to') !!}
                <span class="font-medium text-ink">{{ $paginator->lastItem() }}</span>
            @else
                {{ $paginator->count() }}
            @endif
            {!! __('of') !!}
            <span class="font-medium text-ink">{{ $paginator->total() }}</span>
            {!! __('results') !!}
        </p>

        <div class="flex flex-wrap items-center gap-2">
            @if ($paginator->onFirstPage())
                <span class="inline-flex rounded-full bg-pill px-3 py-1 text-sm text-ink opacity-40" aria-disabled="true">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex rounded-full bg-pill px-3 py-1 text-sm text-ink hover:bg-pill-active">
                    {!! __('pagination.previous') !!}
                </a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="inline-flex px-2 text-sm text-muted" aria-disabled="true">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="inline-flex rounded-full bg-pill-active px-3 py-1 text-sm text-ink" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="inline-flex rounded-full bg-pill px-3 py-1 text-sm text-ink hover:bg-pill-active" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex rounded-full bg-pill px-3 py-1 text-sm text-ink hover:bg-pill-active">
                    {!! __('pagination.next') !!}
                </a>
            @else
                <span class="inline-flex rounded-full bg-pill px-3 py-1 text-sm text-ink opacity-40" aria-disabled="true">
                    {!! __('pagination.next') !!}
                </span>
            @endif
        </div>
    </nav>
@endif
