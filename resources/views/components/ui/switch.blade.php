@props([
    'label',
    'name',
    'checked' => false,
    'icon' => null,
])

<label {{ $attributes->class('flex min-h-11 cursor-pointer items-center justify-between gap-3 rounded-field bg-surface px-3') }}>
    <span class="flex items-center gap-2 text-[15px] font-medium">
        @if ($icon)
            <x-icon :name="$icon" size="5" />
        @endif
        {{ $label }}
    </span>
    <input type="checkbox" name="{{ $name }}" value="1" @checked($checked) class="peer sr-only">
    <span aria-hidden="true" class="flex h-7 w-11.5 shrink-0 items-center rounded-full bg-line p-0.75 peer-checked:justify-end peer-checked:bg-accent peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-ink">
        <span class="size-5.5 rounded-full bg-field shadow-sm"></span>
    </span>
</label>
