@props(['item'])

{{-- One handled item on a list's own page. Unchecking it puts it back on the list. --}}
<div
    @can('unhandle', $item)
        x-data="handleItem"
        data-handle-url="{{ route('lists.items.unhandle', $item) }}"
        x-bind:class="leaving && 'opacity-0'"
    @endcan
    {{ $attributes->class(['flex min-h-13 items-center gap-1 py-2 pr-3 transition-opacity duration-700', 'pl-1' => auth()->user()?->can('unhandle', $item), 'pl-4' => ! auth()->user()?->can('unhandle', $item)]) }}
>
    @can('unhandle', $item)
        <label class="grid size-11 shrink-0 cursor-pointer place-items-center">
            <span class="sr-only">{{ __('lists.unhandle_item') }}</span>
            <input
                type="checkbox"
                checked
                class="size-5.5 rounded-[7px] bg-field shadow-[inset_0_0_0_2px_var(--accent)] checked:bg-primary checked:shadow-none checked:after:border-primary-ink"
                x-on:change="mark($event)"
            >
        </label>
    @endcan

    <div class="min-w-0 flex-1 py-1">
        <p @class(['text-[15px] font-semibold break-words text-ink', 'line-through decoration-muted' => $item->handled_at !== null])>{{ $item->name }}</p>
        @if (filled($item->description))
            <p class="text-[13px] break-words whitespace-pre-line text-muted">{{ $item->description }}</p>
        @endif
        @if ($item->handled_at !== null)
            <p class="text-[13px] text-muted">
                {{ __('lists.handled_by') }} {{ $item->handler?->name }}
                <span aria-hidden="true"> · </span>
                <time datetime="{{ $item->handled_at->timezone(config('app.timezone'))->toDateString() }}">{{ $item->handled_at->timezone(config('app.timezone'))->format('Y-m-d') }}</time>
            </p>
        @endif
    </div>

    @if ($item->is_public)
        <span class="grid size-8 shrink-0 place-items-center text-muted" title="{{ __('lists.public') }}">
            <x-icon name="globe" size="4" />
            <span class="sr-only">{{ __('lists.public') }}</span>
        </span>
    @endif

    @if ($item->estimate !== null)
        <span class="shrink-0 px-1.5 text-sm text-muted tabular-nums">{{ $item->estimate }}</span>
    @endif
</div>
