@props(['thread'])

<article {{ $attributes }}>
    <a href="{{ route('threads.show', $thread) }}" class="block">
        <x-message :author-id="$thread->user_id" :body="$thread->body" :author="$thread->user?->name" :posted-at="$thread->created_at">
            @if (filled($thread->title))
                <p class="mb-2 text-lg font-semibold">{{ $thread->title }}</p>
            @endif
        </x-message>
    </a>

    @if ($thread->replies->isNotEmpty())
        <div class="mt-2 ml-5 space-y-2">
            @foreach ($thread->replies as $reply)
                <x-message compact :author-id="$reply->user_id" :body="$reply->body" :author="$reply->user?->name" :posted-at="$reply->created_at" />
            @endforeach
        </div>
    @endif
</article>
