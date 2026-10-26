@extends('layouts.app')

@section('content')
    <a href="{{ $thread->isArchived() ? route('home', ['archived' => 1]) : route('home') }}" class="inline-flex rounded-full bg-pill px-3 py-1 text-sm text-ink hover:bg-pill-active">{{ __('threads.back') }}</a>

    <div class="mt-4 flex flex-col-reverse gap-4 sm:flex-row sm:items-start sm:gap-8">
        <div class="min-w-0 flex-1">
            @if (filled($thread->title))
                <h1 class="mb-3 inline-flex rounded-full bg-chip px-3 py-1 text-lg font-medium text-ink">{{ $thread->title }}</h1>
            @endif

            <x-message :body="$thread->body" :author="$thread->user?->name" :posted-at="$thread->created_at" />

            @if ($thread->isArchived())
                <p class="mt-3 inline-flex rounded-full bg-lilac px-3 py-1 text-xs text-ink">{{ __('threads.archived_notice') }}</p>
            @endif

            <div class="mt-3 ml-5 space-y-2">
                @foreach ($thread->replies as $reply)
                    <div>
                        <x-message compact tone="reply" :body="$reply->body" :author="$reply->user?->name" :posted-at="$reply->created_at" />

                        @can('delete', $reply)
                            <form method="POST" action="{{ route('replies.destroy', [$thread, $reply]) }}" class="mt-1" onsubmit="return confirm(@js(__('threads.confirm_delete_reply')))">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-full bg-danger-soft px-3 py-1 text-xs font-medium text-danger hover:opacity-90">{{ __('threads.delete_reply') }}</button>
                            </form>
                        @endcan
                    </div>
                @endforeach

                @can('reply', $thread)
                    <form method="POST" action="{{ route('threads.replies.store', $thread) }}" class="space-y-2 rounded-3xl bg-form p-3">
                        @csrf

                        <label class="block text-xs text-muted" for="reply-body">{{ __('threads.reply') }}</label>
                        <textarea id="reply-body" name="body" rows="2" required maxlength="10000" class="w-full rounded-2xl bg-field px-3 py-2 text-sm text-ink">{{ old('body') }}</textarea>

                        @error('body')
                            <p class="rounded-2xl bg-danger-soft px-3 py-2 text-xs text-danger">{{ $message }}</p>
                        @enderror

                        <button type="submit" class="rounded-full bg-button px-3 py-1 text-xs font-medium text-button-ink hover:opacity-90">{{ __('threads.send_reply') }}</button>
                    </form>
                @endcan
            </div>
        </div>

        @canany(['update', 'archive', 'delete'], $thread)
            <aside class="w-full space-y-3 rounded-3xl bg-aside p-4 sm:w-48 sm:shrink-0">
                @can('update', $thread)
                    <form method="POST" action="{{ route('threads.update', $thread) }}" class="space-y-2">
                        @csrf
                        @method('PATCH')

                        <label class="block text-xs text-muted" for="title">{{ __('threads.name') }}</label>
                        <input id="title" name="title" type="text" value="{{ old('title', $thread->title) }}" maxlength="255" class="w-full rounded-2xl bg-field px-3 py-2 text-xs text-ink">

                        @error('title')
                            <p class="rounded-2xl bg-danger-soft px-3 py-2 text-xs text-danger">{{ $message }}</p>
                        @enderror

                        <button type="submit" class="rounded-full bg-button px-3 py-1 text-xs font-medium text-button-ink hover:opacity-90">{{ __('threads.save_name') }}</button>
                    </form>
                @endcan

                <div class="flex flex-col items-start gap-2">
                    @can('archive', $thread)
                        @if ($thread->isArchived())
                            <form method="POST" action="{{ route('threads.unarchive', $thread) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="rounded-full bg-pill px-3 py-1 text-xs text-ink hover:bg-pill-active">{{ __('threads.unarchive') }}</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('threads.archive', $thread) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="rounded-full bg-pill px-3 py-1 text-xs text-ink hover:bg-pill-active">{{ __('threads.archive_thread') }}</button>
                            </form>
                        @endif
                    @endcan

                    @can('delete', $thread)
                        <form method="POST" action="{{ route('threads.destroy', $thread) }}" onsubmit="return confirm(@js(__('threads.confirm_delete_thread')))">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-full bg-danger-soft px-3 py-1 text-xs font-medium text-danger hover:opacity-90">{{ __('threads.delete_thread') }}</button>
                        </form>
                    @endcan
                </div>
            </aside>
        @endcanany
    </div>
@endsection
