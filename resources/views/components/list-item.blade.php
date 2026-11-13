@props(['item'])

{{-- One open item on the lists page. Checking it hands it over to handleItem in app.js. --}}
<div
    data-item
    @can('handle', $item)
        x-data="handleItem"
        data-handle-url="{{ route('lists.items.handle', $item) }}"
        x-bind:class="leaving && 'opacity-0'"
    @endcan
    {{ $attributes->class('flex min-h-13 items-center gap-1 py-1.5 pr-1.5 pl-1 transition-opacity duration-700') }}
>
    @can('handle', $item)
        <label class="grid size-11 shrink-0 cursor-pointer place-items-center">
            <span class="sr-only">{{ __('lists.handle_item') }}</span>
            <input
                type="checkbox"
                class="size-5.5 rounded-[7px] bg-field shadow-[inset_0_0_0_2px_var(--accent)] checked:bg-primary checked:shadow-none checked:after:border-primary-ink"
                x-on:change="mark($event)"
            >
        </label>
    @else
        <span class="w-3 shrink-0"></span>
    @endcan

    <div class="min-w-0 flex-1 py-1" @can('handle', $item) x-bind:class="pending && 'line-through opacity-60'" @endcan>
        <p class="text-[15px] font-semibold break-words text-ink">{{ $item->name }}</p>
        @if (filled($item->description))
            <p class="line-clamp-2 text-[13px] break-words whitespace-pre-line text-muted">{{ $item->description }}</p>
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

    @can('delete', $item)
        <form method="POST" action="{{ route('lists.items.destroy', $item) }}" onsubmit="return confirm(@js(__('lists.confirm_delete_item')))">
            @csrf
            @method('DELETE')

            <x-ui.button type="submit" variant="ghost" icon :aria-label="__('lists.delete_item')">
                <span class="text-muted"><x-icon name="trash" size="5" /></span>
            </x-ui.button>
        </form>
    @endcan
</div>
