@props(['href'])

<header class="sticky top-0 z-10 bg-header text-ink">
    <div class="mx-auto flex max-w-5xl flex-wrap items-center justify-between gap-3 px-4 py-4">
        <a href="{{ $href }}" class="font-semibold leading-snug">{{ __('layout.brand') }}</a>
        {{ $slot }}
    </div>
</header>
