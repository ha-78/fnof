@props([
    'label',
    'name',
    'action' => null,
])

<form
    @if ($action) action="{{ $action }}" method="POST" @endif
    {{ $attributes->class('rounded-card border border-surface-line bg-field py-1 pr-1 pl-3.5') }}
>
    @if ($action)
        @csrf
    @endif

    <div class="flex items-center gap-2">
        <label class="sr-only" for="{{ $name }}">{{ $label }}</label>
        <input
            id="{{ $name }}"
            name="{{ $name }}"
            placeholder="{{ $label }}"
            class="h-11 min-w-0 flex-1 bg-transparent text-[15px] text-field-ink placeholder:text-muted"
        >
        <x-ui.button :type="$action ? 'submit' : 'button'" variant="primary" icon :aria-label="$label">
            <x-icon name="plus" size="5" stroke="1.8" />
        </x-ui.button>
    </div>

    @if ($slot->isNotEmpty())
        <div class="py-2 pr-1.5">
            {{ $slot }}
        </div>
    @endif
</form>
