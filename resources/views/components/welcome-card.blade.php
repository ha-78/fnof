@props(['section' => null, 'color', 'icon' => null, 'title'])

{{-- One part of the app on the welcome page, in that part's own colors. --}}
<article data-color="{{ $color }}" {{ $attributes->class('welcome-card rounded-card border border-surface-line bg-surface p-4') }}>
    <h2 class="flex items-center gap-3 text-[17px] font-semibold">
        <span class="grid size-9 shrink-0 place-items-center rounded-full bg-tonal text-tonal-ink">
            @if ($section)
                <x-section-icon :section="$section" size="5" />
            @else
                <x-icon :name="$icon" size="5" />
            @endif
        </span>
        {{ $title }}
    </h2>
    <p class="mt-3 text-[14px] leading-6">{{ $slot }}</p>
</article>
