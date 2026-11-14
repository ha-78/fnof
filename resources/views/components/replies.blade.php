@props(['thread'])

{{-- The replies under a thread. Swapped in live by liveRegion, which reads data-archived. --}}
<div data-archived="{{ $thread->isArchived() ? '1' : '0' }}" class="space-y-3">
    @foreach ($thread->replies as $reply)
        <x-message :author-id="$reply->user_id" :body="$reply->body" :author="$reply->user?->name" :posted-at="$reply->created_at">
            @can('delete', $reply)
                <form method="POST" action="{{ route('replies.destroy', [$thread, $reply]) }}" onsubmit="return confirm(@js(__('threads.confirm_delete_reply')))">
                    @csrf
                    @method('DELETE')

                    <x-ui.button type="submit" variant="ghost" icon :aria-label="__('threads.delete_reply')">
                        <span class="text-muted"><x-icon name="trash" size="4" /></span>
                    </x-ui.button>
                </form>
            @endcan
        </x-message>
    @endforeach

    @if ($thread->isArchived())
        <p class="flex items-center gap-2 rounded-card border border-surface-line bg-surface px-3 py-2.5 text-[14px] text-muted">
            <x-icon name="archive" size="5" class="shrink-0" />
            {{ __('threads.archived_notice') }}
        </p>
    @endif
</div>
