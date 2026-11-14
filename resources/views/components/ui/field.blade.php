@props([
    'label',
    'name',
    'id' => null,
    'type' => 'text',
    'multiline' => false,
    'options' => null,
    'selected' => null,
    'showError' => true,
])

{{-- A labelled field. "options" (value => label) makes it a select. "id" is needed when two fields share a name;
     "showError" lets a page decide which of them shows the shared error. --}}
@php($id ??= $name)
@php($invalid = $showError && $errors->has($name))

<div>
    <label for="{{ $id }}" class="block text-[13px] font-medium text-muted">{{ $label }}</label>

    @if ($options !== null)
        <select
            {{ $attributes
                ->class([
                    'mt-1 h-11 w-full rounded-field border-2 bg-field px-3 text-[15px] text-field-ink',
                    'border-field-error' => $invalid,
                    'border-transparent' => ! $invalid,
                ])
                ->merge([
                    'id' => $id,
                    'name' => $name,
                    'aria-invalid' => $invalid ? 'true' : null,
                    'aria-describedby' => $invalid ? $id.'-error' : null,
                ]) }}
        >
            @foreach ($options as $value => $optionLabel)
                <option value="{{ $value }}" @selected((string) $selected === (string) $value)>{{ $optionLabel }}</option>
            @endforeach
        </select>
    @elseif ($multiline)
        <textarea
            {{ $attributes
                ->class([
                    'mt-1 min-h-24 w-full rounded-field border-2 bg-field px-3 py-2 text-[15px] text-field-ink',
                    'border-field-error' => $invalid,
                    'border-transparent' => ! $invalid,
                ])
                ->merge([
                    'id' => $id,
                    'name' => $name,
                    'aria-invalid' => $invalid ? 'true' : null,
                    'aria-describedby' => $invalid ? $id.'-error' : null,
                ]) }}
        >{{ $slot }}</textarea>
    @else
        <input
            {{ $attributes
                ->class([
                    'mt-1 h-11 w-full rounded-field border-2 bg-field px-3 text-[15px] text-field-ink',
                    'border-field-error' => $invalid,
                    'border-transparent' => ! $invalid,
                ])
                ->merge([
                    'id' => $id,
                    'name' => $name,
                    'type' => $type,
                    'aria-invalid' => $invalid ? 'true' : null,
                    'aria-describedby' => $invalid ? $id.'-error' : null,
                ]) }}
        >
    @endif

    @if ($invalid)
        <p id="{{ $id }}-error" class="mt-1 flex items-center gap-1.5 text-[13px] font-semibold text-field-error-ink">
            <x-icon name="exclamation-circle" size="4" stroke="2" class="shrink-0" />
            {{ $errors->first($name) }}
        </p>
    @endif
</div>
