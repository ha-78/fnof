@props(['thread'])

{{-- One thread in a list: name or opening message, the latest reply, and how many replies. --}}
<a href="{{ route('threads.show', $thread) }}" {{ $attributes->class('flex gap-3 px-4 py-3 text-ink') }}>
    <span aria-hidden="true" class="grid size-9 shrink-0 place-items-center rounded-full bg-tonal text-[14px] font-semibold text-tonal-ink">{{ Str::upper(Str::substr($thread->user?->name ?? '?', 0, 1)) }}</span>

    <span class="flex min-w-0 flex-1 flex-col gap-0.5">
        <span class="flex items-start gap-2">
            @if (filled($thread->title))
                <span class="min-w-0 flex-1 truncate text-[15px] font-semibold">{{ $thread->title }}</span>
            @else
                <span class="line-clamp-2 min-w-0 flex-1 text-[15px] font-semibold break-words">{{ $thread->body }}</span>
            @endif

            @if ($thread->replies->isNotEmpty())
                <x-ui.badge class="mt-0.5 shrink-0">{{ trans_choice('threads.replies', $thread->replies->count()) }}</x-ui.badge>
            @endif
        </span>

        @if (filled($thread->title))
            <span class="line-clamp-2 text-[14px] leading-snug break-words">{{ $thread->body }}</span>
        @endif

        @if ($thread->replies->isNotEmpty())
            <span class="flex items-baseline gap-1.5 text-[13px] text-muted">
                <span class="min-w-0 flex-1 truncate">
                    <span class="font-semibold"><x-author-name :author-id="$thread->replies->last()->user_id" :name="$thread->replies->last()->user?->name" />:</span>
                    {{ $thread->replies->last()->body }}
                </span>
                <time class="shrink-0 text-[12px]" datetime="{{ $thread->replies->last()->created_at->toAtomString() }}" data-posted data-relative>{{ \App\Support\RelativeDate::label($thread->replies->last()->created_at) }}</time>
            </span>
        @endif

        <span class="text-[12px] text-muted">
            <x-author-name :author-id="$thread->user_id" :name="$thread->user?->name" />
            <span aria-hidden="true"> · </span>
            <time datetime="{{ $thread->created_at->toAtomString() }}" data-posted data-relative>{{ \App\Support\RelativeDate::label($thread->created_at) }}</time>
        </span>
    </span>
</a>
