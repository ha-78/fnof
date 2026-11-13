@props([
    'heading' => null,
])

<section {{ $attributes }}>
    @if (filled($heading))
        <h2 class="px-1 pb-1.5 text-[13px] font-semibold uppercase tracking-wide text-muted">{{ $heading }}</h2>
    @endif

    <div class="divide-y divide-surface-line overflow-hidden rounded-card border border-surface-line bg-surface">
        {{ $slot }}
    </div>
</section>
