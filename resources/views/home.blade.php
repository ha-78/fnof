@extends('layouts.app')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-stone-500">{{ __('home.welcome') }}</p>
            <h1 class="mt-1 text-3xl font-semibold tracking-tight">{{ $user->name }}</h1>
        </div>

        @if ($showingArchived)
            <a href="{{ route('home') }}" class="text-sm underline-offset-2 hover:underline">{{ __('threads.open') }}</a>
        @else
            <a href="{{ route('home', ['archived' => 1]) }}" class="text-sm underline-offset-2 hover:underline">{{ __('threads.archive') }}</a>
        @endif
    </div>

    @unless ($showingArchived)
        <form method="POST" action="{{ route('threads.store') }}" class="mt-8 space-y-3 rounded-lg border border-stone-200 bg-white p-4">
            @csrf

            <label class="block text-sm font-medium" for="body">{{ __('threads.new') }}</label>
            <textarea id="body" name="body" rows="4" required maxlength="10000" class="w-full rounded-md border border-stone-300 bg-white px-3 py-2 text-sm">{{ old('body') }}</textarea>

            @error('body')
                <p class="text-sm text-red-700">{{ $message }}</p>
            @enderror

            <button type="submit" class="rounded-md bg-stone-900 px-3 py-2 text-sm text-white">{{ __('threads.publish') }}</button>
        </form>
    @endunless

    <section class="mt-8">
        <h2 class="text-lg font-medium">{{ $listHeading }}</h2>

        @if ($threads->isEmpty())
            <p class="mt-3 text-sm text-stone-600">{{ $emptyMessage }}</p>
        @else
            <div class="mt-4 space-y-8">
                @foreach ($threads as $thread)
                    <article>
                        @if (filled($thread->title))
                            <a href="{{ route('threads.show', $thread) }}" class="mb-2 inline-block text-sm font-medium underline-offset-2 hover:underline">{{ $thread->title }}</a>
                        @endif

                        <a href="{{ route('threads.show', $thread) }}" class="block">
                            <x-message :body="$thread->body" :author="$thread->user?->name" :posted-at="$thread->created_at" />
                        </a>

                        @if ($thread->replies->isNotEmpty())
                            <div class="mt-2 ml-5 space-y-2 border-l border-stone-200 pl-3">
                                @foreach ($thread->replies as $reply)
                                    <x-message compact :body="$reply->body" :author="$reply->user?->name" :posted-at="$reply->created_at" />
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
