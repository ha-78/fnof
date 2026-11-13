@props([
    'selected' => false,
    'when' => null,
])

{{-- "when" is an Alpine expression; with it the chip follows page state instead of the server. --}}
<button
    type="button"
    aria-pressed="{{ $selected ? 'true' : 'false' }}"
    @if ($when)
        x-bind:aria-pressed="({{ $when }}).toString()"
        x-bind:class="{ 'bg-primary text-primary-ink': {{ $when }}, 'bg-tonal text-tonal-ink': ! ({{ $when }}) }"
    @endif
    {{ $attributes->class([
        'inline-flex h-9 shrink-0 items-center gap-1.5 rounded-full px-3 text-sm font-semibold whitespace-nowrap',
        'bg-primary text-primary-ink' => $selected,
        'bg-tonal text-tonal-ink' => ! $selected,
    ]) }}
>{{ $slot }}</button>
