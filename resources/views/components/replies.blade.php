@props(['thread'])

<div data-archived="{{ $thread->isArchived() ? '1' : '0' }}">
    @if ($thread->isArchived())
        <p class="mt-3 inline-flex rounded-full bg-lilac px-3 py-1 text-xs text-ink">{{ __('threads.archived_notice') }}</p>
    @endif

    @if ($thread->replies->isNotEmpty())
        <div class="mt-3 ml-5 space-y-2">
            @foreach ($thread->replies as $reply)
                <div>
                    <x-message compact :author-id="$reply->user_id" :body="$reply->body" :author="$reply->user?->name" :posted-at="$reply->created_at" />

                    @can('delete', $reply)
                        <form method="POST" action="{{ route('replies.destroy', [$thread, $reply]) }}" class="mt-1" onsubmit="return confirm(@js(__('threads.confirm_delete_reply')))">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-full bg-danger-soft px-3 py-1 text-xs font-medium text-danger hover:opacity-90">{{ __('threads.delete_reply') }}</button>
                        </form>
                    @endcan
                </div>
            @endforeach
        </div>
    @endif
</div>
