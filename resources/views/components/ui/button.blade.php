@props([
    'variant' => 'primary',
    'type' => 'button',
    'icon' => false,
])

<button
    type="{{ $type }}"
    @if ($variant === 'danger') data-color="{{ \App\Enums\Section::Calendar->color() }}" @endif
    {{ $attributes->class([
        'inline-flex items-center justify-center gap-1.5 rounded-field text-[15px] font-semibold',
        'size-11 shrink-0 p-0' => $icon,
        'h-11 px-4' => ! $icon,
        'bg-primary text-primary-ink' => $variant === 'primary',
        'bg-tonal text-tonal-ink' => $variant === 'tonal',
        'text-ink' => $variant === 'ghost',
        'bg-surface text-accent' => $variant === 'danger',
    ]) }}
>
    {{ $slot }}
</button>
