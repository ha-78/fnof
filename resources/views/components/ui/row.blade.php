@props([
    'title',
    'meta' => null,
    'href' => null,
])

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class('flex min-h-13 items-center gap-3 px-4 py-2 text-ink') }}>
        <span class="min-w-0 flex-1">
            <span class="block text-[15px] font-semibold">{{ $title }}</span>
            @if (filled($meta))
                <span class="block text-[13px] text-muted">{{ $meta }}</span>
            @endif
        </span>
        {{ $slot }}
    </a>
@else
    <div {{ $attributes->class('flex min-h-13 items-center gap-3 px-4 py-2') }}>
        <span class="min-w-0 flex-1">
            <span class="block text-[15px] font-semibold text-ink">{{ $title }}</span>
            @if (filled($meta))
                <span class="block text-[13px] text-muted">{{ $meta }}</span>
            @endif
        </span>
        {{ $slot }}
    </div>
@endif
