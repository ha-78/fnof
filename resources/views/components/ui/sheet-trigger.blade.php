@props([
    'for',
    'variant' => 'tonal',
    'icon' => false,
    'label' => null,
])

<x-ui.button
    type="button"
    :variant="$variant"
    :icon="$icon"
    :aria-label="$label"
    aria-haspopup="dialog"
    x-data
    x-on:click="$dispatch('open-sheet', '{{ $for }}')"
    {{ $attributes }}
>
    {{ $slot }}
</x-ui.button>
