@props(['thread'])

<article {{ $attributes }}>
    <a href="{{ route('threads.show', $thread) }}" class="block">
        <x-message relative-date :author-id="$thread->user_id" :body="$thread->body" :author="$thread->user?->name" :posted-at="$thread->created_at">
            @if (filled($thread->title))
                <p class="mb-2 text-lg font-semibold">{{ $thread->title }}</p>
            @endif

            @if ($thread->replies->isNotEmpty())
                <x-slot:footer>
                    <div class="thread-replies mt-4 ml-5 space-y-4">
                        @foreach ($thread->replies as $reply)
                            <x-message plain compact relative-date :author-id="$reply->user_id" :body="$reply->body" :author="$reply->user?->name" :posted-at="$reply->created_at" />
                        @endforeach
                    </div>
                </x-slot:footer>
            @endif
        </x-message>
    </a>
</article>
