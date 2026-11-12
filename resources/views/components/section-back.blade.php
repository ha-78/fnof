@props(['section'])

<a href="{{ route($section->routeName()) }}" aria-label="{{ $section->label() }}" {{ $attributes->class('mb-6 inline-flex size-9 items-center justify-center rounded-full border-2 border-current text-[var(--section-mark)]') }}>
    <x-icon name="chevron-left" size="5" />
</a>
