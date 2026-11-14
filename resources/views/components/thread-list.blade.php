@props(['threads', 'emptyMessage'])

@if ($threads->isEmpty())
    <p class="rounded-card border border-surface-line bg-surface px-4 py-3 text-[15px] text-muted">{{ $emptyMessage }}</p>
@else
    <x-ui.group>
        @foreach ($threads as $thread)
            <x-thread :thread="$thread" />
        @endforeach
    </x-ui.group>

    @if ($threads->hasPages())
        <div class="mt-4">{{ $threads->links() }}</div>
    @endif
@endif
