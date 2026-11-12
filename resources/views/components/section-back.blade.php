@props(['section'])

<a href="{{ route($section->routeName()) }}" aria-label="{{ $section->label() }}" {{ $attributes->class('grid size-11 shrink-0 place-items-center rounded-full text-ink') }}>
    <x-icon name="chevron-left" size="5" />
</a>
