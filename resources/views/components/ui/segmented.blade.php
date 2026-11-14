@props([
    'label',
    'options' => [],
])

{{-- All in the section's colors: the track in step 2, the chosen option like a primary button. Options with an "href" are links; the others are buttons. --}}
<div role="group" aria-label="{{ $label }}" {{ $attributes->class('flex gap-0.5 rounded-card bg-tonal p-0.5') }}>
    @foreach ($options as $option)
        @if (isset($option['href']))
            <a
                href="{{ $option['href'] }}"
                @if ($option['selected'] ?? false) aria-current="page" @endif
                @class([
                    'grid h-9 flex-1 place-items-center rounded-field px-3 text-sm',
                    'bg-primary font-semibold text-primary-ink' => $option['selected'] ?? false,
                    'font-medium text-tonal-ink' => ! ($option['selected'] ?? false),
                ])
            >{{ $option['label'] }}</a>
        @elseif (isset($option['when']))
            {{-- Follows page state: "when" and "click" are Alpine expressions. --}}
            <button
                type="button"
                aria-pressed="false"
                x-bind:aria-pressed="({{ $option['when'] }}).toString()"
                x-bind:class="{ 'bg-primary font-semibold text-primary-ink': {{ $option['when'] }}, 'font-medium text-tonal-ink': ! ({{ $option['when'] }}) }"
                x-on:click="{{ $option['click'] }}"
                class="h-9 flex-1 rounded-field px-3 text-sm font-medium text-tonal-ink"
            >{{ $option['label'] }}</button>
        @else
            <button
                type="button"
                aria-pressed="{{ ($option['selected'] ?? false) ? 'true' : 'false' }}"
                @class([
                    'h-9 flex-1 rounded-field px-3 text-sm',
                    'bg-primary font-semibold text-primary-ink' => $option['selected'] ?? false,
                    'font-medium text-tonal-ink' => ! ($option['selected'] ?? false),
                ])
            >{{ $option['label'] }}</button>
        @endif
    @endforeach
</div>
