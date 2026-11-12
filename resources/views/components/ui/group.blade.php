@props([
    'heading' => null,
])

<section {{ $attributes->class('overflow-hidden rounded-card border border-surface-line bg-surface') }}>
    @if (filled($heading))
        <h2 class="px-4 pt-3 text-[13px] font-semibold uppercase tracking-wide text-muted">{{ $heading }}</h2>
    @endif

    <div class="divide-y divide-surface-line">
        {{ $slot }}
    </div>
</section>
