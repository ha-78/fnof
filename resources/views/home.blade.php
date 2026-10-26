@extends('layouts.app')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        @if ($showingArchived)
            <a href="{{ route('home') }}" class="inline-flex rounded-full bg-pill px-3 py-1 text-sm text-ink hover:bg-pill-active">{{ __('threads.open') }}</a>
        @else
            <a href="{{ route('home', ['archived' => 1]) }}" class="inline-flex rounded-full bg-pill px-3 py-1 text-sm text-ink hover:bg-pill-active">{{ __('threads.archive') }}</a>
        @endif
    </div>

    @unless ($showingArchived)
        <form method="POST" action="{{ route('threads.store') }}" class="mt-8 space-y-3 rounded-3xl bg-form p-5">
            @csrf

            <label class="block text-sm font-medium" for="body">{{ __('threads.new') }}</label>
            <textarea id="body" name="body" rows="4" required maxlength="10000" class="w-full rounded-2xl bg-field px-3 py-2 text-sm text-ink">{{ old('body') }}</textarea>

            @error('body')
                <p class="rounded-2xl bg-danger-soft px-3 py-2 text-sm text-danger">{{ $message }}</p>
            @enderror

            <button type="submit" class="rounded-full bg-button px-4 py-2 text-sm font-medium text-button-ink hover:opacity-90">{{ __('threads.publish') }}</button>
        </form>
    @endunless

    <section class="mt-8">
        <h2 class="text-lg font-medium">{{ $listHeading }}</h2>

        @if ($threads->isEmpty())
            <p class="mt-3 rounded-3xl bg-lilac px-4 py-3 text-sm text-ink">{{ $emptyMessage }}</p>
        @else
            <div class="mt-4 space-y-8">
                @foreach ($threads as $thread)
                    <article>
                        @if (filled($thread->title))
                            <a href="{{ route('threads.show', $thread) }}" class="mb-2 inline-flex rounded-full bg-chip px-3 py-1 text-sm font-medium text-ink">{{ $thread->title }}</a>
                        @endif

                        <a href="{{ route('threads.show', $thread) }}" class="block">
                            <x-message :tone-index="$loop->index" :body="$thread->body" :author="$thread->user?->name" :posted-at="$thread->created_at" />
                        </a>

                        @if ($thread->replies->isNotEmpty())
                            <div class="mt-2 ml-5 space-y-2">
                                @foreach ($thread->replies as $reply)
                                    <x-message compact tone="reply" :body="$reply->body" :author="$reply->user?->name" :posted-at="$reply->created_at" />
                                @endforeach
                            </div>
                        @endif
                    </article>
                @endforeach
            </div>

            <div class="mt-6">{{ $threads->links() }}</div>
        @endif
    </section>
@endsection
