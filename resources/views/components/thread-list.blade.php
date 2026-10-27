@props(['threads', 'emptyMessage'])

@if ($threads->isEmpty())
    <p class="mt-3 rounded-3xl bg-lilac px-4 py-3 text-sm text-ink">{{ $emptyMessage }}</p>
@else
    <div class="mt-4 space-y-8">
        @foreach ($threads as $thread)
            <x-thread :thread="$thread" />
        @endforeach
    </div>

    <div class="mt-6">{{ $threads->links() }}</div>
@endif
