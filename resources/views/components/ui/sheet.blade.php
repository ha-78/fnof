@props([
    'title',
    'name' => null,
    'close' => null,
    'open' => false,
    'triggerVariant' => 'tonal',
    'triggerIcon' => false,
    'triggerLabel' => null,
])

<div
    x-data="{ open: @js((bool) $open) }"
    @if (filled($name)) x-on:open-sheet.window="$event.detail === @js($name) && (open = true)" @endif
    {{ $attributes }}
>
    @isset($trigger)
    <x-ui.button
        type="button"
        :variant="$triggerVariant"
        :icon="$triggerIcon"
        :aria-label="$triggerLabel"
        aria-haspopup="dialog"
        x-bind:aria-expanded="open.toString()"
        x-on:click="open = true"
    >
        {{ $trigger }}
    </x-ui.button>
    @endisset

    <div
        x-show="open"
        x-cloak
        class="fixed inset-0 z-40 flex items-end bg-ink/30 sm:items-center sm:justify-center sm:p-4"
        x-on:click="open = false"
        x-on:keydown.escape.window="open = false"
    >
        <div
            role="dialog"
            aria-modal="true"
            aria-label="{{ $title }}"
            class="max-h-[85vh] w-full overflow-y-auto rounded-t-sheet bg-canvas px-4 pt-2 pb-5 text-ink sm:max-w-md sm:rounded-sheet sm:pt-4"
            x-on:click.stop
            x-init="$watch('open', (value) => value && $nextTick(() => $el.querySelector('input:not([type=hidden]), textarea')?.focus()))"
        >
            <span aria-hidden="true" class="mx-auto mb-2 block h-1.5 w-10 rounded-full bg-line sm:hidden"></span>

            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-semibold">{{ $title }}</h2>

                @if (filled($close))
                    <x-ui.button type="button" variant="tonal" icon :aria-label="$close" x-on:click="open = false">
                        <x-icon name="x" size="5" stroke="2" />
                    </x-ui.button>
                @endif
            </div>

            <div class="mt-3">{{ $slot }}</div>
        </div>
    </div>
</div>
