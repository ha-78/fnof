@props([
    'type' => 'success',
    'dismissible' => true,
    'fade' => false,
])

<div
    role="{{ $type === 'error' ? 'alert' : 'status' }}"
    @if ($dismissible || $fade) x-data="{ shown: true }" x-show="shown" @endif
    @if ($fade) x-init="setTimeout(() => shown = false, 5000)" x-transition.opacity.duration.300ms @endif
    {{ $attributes->class([
        'flex min-h-12 items-center gap-2.5 rounded-card border py-1.5 pr-1.5 pl-3',
        'border-success-line bg-success text-success-ink' => $type !== 'error',
        'border-error bg-error text-error-ink' => $type === 'error',
    ]) }}
>
    <x-icon
        :name="$type === 'error' ? 'exclamation-circle' : 'check-circle'"
        size="5"
        stroke="1.8"
        :class="$type === 'error' ? 'shrink-0' : 'shrink-0 text-success-icon'"
    />

    <p class="min-w-0 flex-1 text-[15px] leading-snug font-medium">{{ $slot }}</p>

    @if ($dismissible)
        <button
            type="button"
            aria-label="{{ __('layout.close') }}"
            class="grid size-9 shrink-0 place-items-center rounded-full opacity-80 hover:opacity-100"
            x-on:click="shown = false"
        >
            <x-icon name="x" size="5" stroke="2" />
        </button>
    @endif
</div>
