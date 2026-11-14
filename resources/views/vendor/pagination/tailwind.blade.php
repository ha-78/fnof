@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-[13px] text-muted">
            {!! __('Showing') !!}
            @if ($paginator->firstItem())
                <span class="font-semibold text-ink">{{ $paginator->firstItem() }}</span>
                {!! __('to') !!}
                <span class="font-semibold text-ink">{{ $paginator->lastItem() }}</span>
            @else
                {{ $paginator->count() }}
            @endif
            {!! __('of') !!}
            <span class="font-semibold text-ink">{{ $paginator->total() }}</span>
            {!! __('results') !!}
        </p>

        <div class="flex flex-wrap items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="inline-flex h-9 items-center gap-1 rounded-field bg-tonal px-2.5 text-sm font-semibold text-tonal-ink opacity-40" aria-disabled="true">
                    <x-icon name="chevron-left" size="4" stroke="2" />
                    <span class="sr-only sm:not-sr-only">{!! __('pagination.previous') !!}</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex h-9 items-center gap-1 rounded-field bg-tonal px-2.5 text-sm font-semibold text-tonal-ink">
                    <x-icon name="chevron-left" size="4" stroke="2" />
                    <span class="sr-only sm:not-sr-only">{!! __('pagination.previous') !!}</span>
                </a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="inline-flex px-1.5 text-sm text-muted" aria-disabled="true">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="inline-grid h-9 min-w-9 place-items-center rounded-field bg-primary px-2 text-sm font-semibold text-primary-ink tabular-nums" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="inline-grid h-9 min-w-9 place-items-center rounded-field bg-tonal px-2 text-sm font-semibold text-tonal-ink tabular-nums" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex h-9 items-center gap-1 rounded-field bg-tonal px-2.5 text-sm font-semibold text-tonal-ink">
                    <span class="sr-only sm:not-sr-only">{!! __('pagination.next') !!}</span>
                    <x-icon name="chevron-right" size="4" stroke="2" />
                </a>
            @else
                <span class="inline-flex h-9 items-center gap-1 rounded-field bg-tonal px-2.5 text-sm font-semibold text-tonal-ink opacity-40" aria-disabled="true">
                    <span class="sr-only sm:not-sr-only">{!! __('pagination.next') !!}</span>
                    <x-icon name="chevron-right" size="4" stroke="2" />
                </span>
            @endif
        </div>
    </nav>
@endif
