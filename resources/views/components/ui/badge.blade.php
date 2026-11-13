@props([
    'icon' => null,
])

<span {{ $attributes->class('inline-flex items-center gap-1 rounded-full bg-tonal px-2 py-0.5 text-[12px] font-semibold text-tonal-ink') }}>
    @if ($icon)
        <x-icon :name="$icon" size="3.5" stroke="2" class="shrink-0" />
    @endif
    {{ $slot }}
</span>
