@props(['section'])

<a href="{{ route($section->routeName()) }}" aria-label="{{ $section->label() }}" {{ $attributes->class('mb-6 inline-flex size-9 items-center justify-center rounded-full border-2 border-current text-[var(--section-mark)]') }}>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
    </svg>
</a>
