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
            {{ $attributes->merge([
                'id' => $name,
                'name' => $name,
                'class' => 'mt-1 min-h-24 w-full rounded-field bg-field px-3 py-2 text-[15px] text-field-ink',
            ]) }}
        >{{ $slot }}</textarea>
    @else
        <input
            {{ $attributes->merge([
                'id' => $name,
                'name' => $name,
                'type' => $type,
                'class' => 'mt-1 h-11 w-full rounded-field bg-field px-3 text-[15px] text-field-ink',
            ]) }}
        >
    @endif

    @error($name)
        <p class="mt-1 text-[13px] text-accent" data-color="{{ \App\Enums\Section::Calendar->color() }}">{{ $message }}</p>
    @enderror
</div>
