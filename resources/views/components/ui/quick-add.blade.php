@props([
    'label',
    'name',
    'placeholder' => null,
    'action' => null,
    'value' => null,
    'expandable' => false,
    'moreLabel' => null,
    'expanded' => false,
])

{{-- One card: heading, the main field with its add button, and any extra fields below a divider. --}}
<form
    @if ($action) action="{{ $action }}" method="POST" @endif
    aria-labelledby="{{ $name }}-heading"
    @if ($expandable) x-data="{ more: @js((bool) $expanded) }" @endif
    {{ $attributes->class('rounded-card border border-surface-line bg-surface p-3 shadow-sm') }}
>
    @if ($action)
        @csrf
    @endif

    {{ $hidden ?? '' }}

    <div class="mb-2 flex min-h-8 items-center justify-between gap-3 px-0.5">
        <h2 id="{{ $name }}-heading" class="flex items-center gap-1.5 text-[13px] font-semibold uppercase tracking-wide text-muted">
            <x-icon name="plus" size="4" stroke="2.2" class="shrink-0" />
            {{ $label }}
        </h2>

        @if ($expandable && $slot->isNotEmpty())
            <button
                type="button"
                class="inline-flex h-8 items-center gap-1 rounded-field bg-tonal px-2.5 text-[13px] font-semibold text-tonal-ink"
                x-bind:aria-expanded="more.toString()"
                aria-controls="{{ $name }}-more"
                x-on:click="more = ! more"
            >
                {{ $moreLabel }}
                <span class="inline-flex transition-transform" x-bind:class="more && 'rotate-180'">
                    <x-icon name="chevron-down" size="4" stroke="2" />
                </span>
            </button>
        @endif
    </div>

    <div class="flex items-center gap-2">
        <label class="sr-only" for="{{ $name }}">{{ $placeholder ?? $label }}</label>
        <input
            id="{{ $name }}"
            name="{{ $name }}"
            value="{{ $value }}"
            placeholder="{{ $placeholder ?? $label }}"
            @error($name) aria-invalid="true" aria-describedby="{{ $name }}-error" @enderror
            @class([
                'h-11 min-w-0 flex-1 rounded-field border-2 bg-field px-3 text-[15px] text-field-ink placeholder:text-field-ink/60',
                'border-field-error' => $errors->has($name),
                'border-transparent' => ! $errors->has($name),
            ])
        >

        <x-ui.button :type="$action ? 'submit' : 'button'" variant="primary" icon :aria-label="$label">
            <x-icon name="plus" size="5" stroke="1.8" />
        </x-ui.button>
    </div>

    @error($name)
        <p id="{{ $name }}-error" class="mt-1.5 flex items-center gap-1.5 text-[13px] font-semibold text-field-error-ink">
            <x-icon name="exclamation-circle" size="4" stroke="2" class="shrink-0" />
            {{ $message }}
        </p>
    @enderror

    @if ($slot->isNotEmpty())
        <div
            id="{{ $name }}-more"
            class="mt-3 space-y-3 border-t border-surface-line pt-3"
            @if ($expandable) x-show="more" @unless ($expanded) x-cloak @endunless @endif
        >
            {{ $slot }}
        </div>
    @endif
</form>
