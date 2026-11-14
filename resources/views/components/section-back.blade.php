@props(['section', 'href' => null])

{{-- Back to the section's main page, or to "href" when a page names a closer parent. --}}
<a href="{{ $href ?: route($section->routeName()) }}" aria-label="{{ $section->label() }}" {{ $attributes->class('grid size-11 shrink-0 place-items-center rounded-full text-ink') }}>
    <x-icon name="chevron-left" size="5" />
</a>
