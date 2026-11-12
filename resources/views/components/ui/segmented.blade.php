@props([
    'label',
    'options' => [],
])

<div role="group" aria-label="{{ $label }}" {{ $attributes->class('flex gap-0.5 rounded-card bg-tonal p-0.5') }}>
    @foreach ($options as $option)
        <button
            type="button"
            aria-pressed="{{ ($option['selected'] ?? false) ? 'true' : 'false' }}"
            @class([
                'h-9 flex-1 rounded-field px-3 text-sm',
                'bg-field font-semibold text-field-ink shadow-sm' => $option['selected'] ?? false,
                'font-medium text-ink' => ! ($option['selected'] ?? false),
            ])
        >{{ $option['label'] }}</button>
    @endforeach
</div>
