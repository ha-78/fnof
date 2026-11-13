@props([
    'label',
    'name',
    'type' => 'text',
    'multiline' => false,
])

<div>
    <label for="{{ $name }}" class="block text-[13px] font-medium text-muted">{{ $label }}</label>

    @if ($multiline)
        <textarea
            {{ $attributes
                ->class([
                    'mt-1 min-h-24 w-full rounded-field border-2 bg-field px-3 py-2 text-[15px] text-field-ink',
                    'border-field-error' => $errors->has($name),
                    'border-transparent' => ! $errors->has($name),
                ])
                ->merge([
                    'id' => $name,
                    'name' => $name,
                    'aria-invalid' => $errors->has($name) ? 'true' : null,
                    'aria-describedby' => $errors->has($name) ? $name.'-error' : null,
                ]) }}
        >{{ $slot }}</textarea>
    @else
        <input
            {{ $attributes
                ->class([
                    'mt-1 h-11 w-full rounded-field border-2 bg-field px-3 text-[15px] text-field-ink',
                    'border-field-error' => $errors->has($name),
                    'border-transparent' => ! $errors->has($name),
                ])
                ->merge([
                    'id' => $name,
                    'name' => $name,
                    'type' => $type,
                    'aria-invalid' => $errors->has($name) ? 'true' : null,
                    'aria-describedby' => $errors->has($name) ? $name.'-error' : null,
                ]) }}
        >
    @endif

    @error($name)
        <p id="{{ $name }}-error" class="mt-1 flex items-center gap-1.5 text-[13px] font-semibold text-field-error-ink">
            <x-icon name="exclamation-circle" size="4" stroke="2" class="shrink-0" />
            {{ $message }}
        </p>
    @enderror
</div>
