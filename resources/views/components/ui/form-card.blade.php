@props([
    'label',
    'action',
    'method' => 'POST',
    'icon' => null,
])

{{-- A regular form in the same card as quick-add: heading on top, fields and buttons below. --}}
<form method="POST" action="{{ $action }}" aria-label="{{ $label }}" {{ $attributes->class('space-y-3 rounded-card border border-surface-line bg-surface p-3 shadow-sm') }}>
    @csrf
    @if (strtoupper($method) !== 'POST')
        @method($method)
    @endif

    <h2 class="flex items-center gap-1.5 px-0.5 text-[13px] font-semibold uppercase tracking-wide text-muted">
        @if ($icon)
            <x-icon :name="$icon" size="4" stroke="2.2" class="shrink-0" />
        @endif
        {{ $label }}
    </h2>

    {{ $slot }}
</form>
