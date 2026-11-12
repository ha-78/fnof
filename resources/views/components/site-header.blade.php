@props(['href' => null])

<header class="sticky top-0 z-10 bg-header text-ink">
    <div class="mx-auto flex max-w-5xl flex-wrap items-center gap-3 px-4 py-4 {{ $href ? 'justify-between' : 'justify-end' }}">
        @if ($href)
            <a href="{{ $href }}" class="font-semibold leading-snug">{{ __('layout.brand') }}</a>
        @endif
        {{ $slot }}
    </div>
</header>
