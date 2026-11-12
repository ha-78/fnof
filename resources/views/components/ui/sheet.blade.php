@props([
    'title',
    'close' => null,
])

<div x-data="{ open: false }" {{ $attributes }}>
    <x-ui.button type="button" variant="tonal" x-on:click="open = true">
        {{ $trigger }}
    </x-ui.button>

    <div
        x-show="open"
        x-cloak
        class="fixed inset-0 z-40 flex items-end bg-ink/30"
        x-on:click="open = false"
        x-on:keydown.escape.window="open = false"
    >
        <div
            role="dialog"
            aria-modal="true"
            aria-label="{{ $title }}"
            class="max-h-[80vh] w-full overflow-y-auto rounded-t-sheet bg-canvas px-4 pt-3 pb-5 text-ink"
            x-on:click.stop
        >
            <h2 class="text-[15px] font-semibold">{{ $title }}</h2>
            <div class="mt-3">{{ $slot }}</div>
            @if (filled($close))
                <x-ui.button type="button" variant="ghost" class="mt-4" x-on:click="open = false">{{ $close }}</x-ui.button>
            @endif
        </div>
    </div>
</div>
