@props([
    'selected' => false,
])

<button
    type="button"
    aria-pressed="{{ $selected ? 'true' : 'false' }}"
    {{ $attributes->class([
        'inline-flex h-9 items-center gap-1.5 rounded-full px-3 text-sm font-semibold',
        'bg-primary text-primary-ink' => $selected,
        'bg-tonal text-tonal-ink' => ! $selected,
    ]) }}
>{{ $slot }}</button>
