@extends('layouts.app')

@section('content')
    <a href="{{ $thread->isArchived() ? route('home', ['archived' => 1]) : route('home') }}" class="text-sm underline-offset-2 hover:underline">{{ __('threads.back') }}</a>

    <div class="mt-4 flex flex-col-reverse gap-4 sm:flex-row sm:items-start sm:gap-8">
        <div class="min-w-0 flex-1">
            @if (filled($thread->title))
                <h1 class="mb-2 text-lg font-medium">{{ $thread->title }}</h1>
            @endif

            <x-message :body="$thread->body" :author="$thread->user?->name" :posted-at="$thread->created_at" />

            @if ($thread->isArchived())
                <p class="mt-2 text-xs text-stone-500">{{ __('threads.archived_notice') }}</p>
            @endif

            <div class="mt-2 ml-5 space-y-2 border-l border-stone-200 pl-3">
                @foreach ($thread->replies as $reply)
                    <div>
                        <x-message compact :body="$reply->body" :author="$reply->user?->name" :posted-at="$reply->created_at" />

                        @can('delete', $reply)
                            <form method="POST" action="{{ route('replies.destroy', [$thread, $reply]) }}" class="mt-1" onsubmit="return confirm(@js(__('threads.confirm_delete_reply')))">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-red-700 underline-offset-2 hover:underline">{{ __('threads.delete_reply') }}</button>
                            </form>
                        @endcan
                    </div>
                @endforeach

                @can('reply', $thread)
                    <form method="POST" action="{{ route('threads.replies.store', $thread) }}" class="space-y-2 pt-1">
                        @csrf

                        <label class="block text-xs text-stone-500" for="reply-body">{{ __('threads.reply') }}</label>
                        <textarea id="reply-body" name="body" rows="2" required maxlength="10000" class="w-full rounded-md border border-stone-300 bg-white px-2 py-1.5 text-sm">{{ old('body') }}</textarea>

                        @error('body')
                            <p class="text-xs text-red-700">{{ $message }}</p>
                        @enderror

                        <button type="submit" class="rounded-md bg-stone-900 px-2 py-1 text-xs text-white">{{ __('threads.send_reply') }}</button>
                    </form>
                @endcan
            </div>
        </div>

        @canany(['update', 'archive', 'delete'], $thread)
            <aside class="w-full space-y-3 sm:w-40 sm:shrink-0">
                @can('update', $thread)
                    <form method="POST" action="{{ route('threads.update', $thread) }}" class="space-y-1">
                        @csrf
                        @method('PATCH')

                        <label class="block text-xs text-stone-500" for="title">{{ __('threads.name') }}</label>
                        <input id="title" name="title" type="text" value="{{ old('title', $thread->title) }}" maxlength="255" class="w-full rounded border border-stone-300 bg-white px-2 py-1 text-xs">

                        @error('title')
                            <p class="text-xs text-red-700">{{ $message }}</p>
                        @enderror

                        <button type="submit" class="text-xs text-stone-500 underline-offset-2 hover:underline">{{ __('threads.save_name') }}</button>
                    </form>
                @endcan

                <div class="flex flex-col items-start gap-1">
                    @can('archive', $thread)
                        @if ($thread->isArchived())
                            <form method="POST" action="{{ route('threads.unarchive', $thread) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-xs text-stone-500 underline-offset-2 hover:underline">{{ __('threads.unarchive') }}</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('threads.archive', $thread) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-xs text-stone-500 underline-offset-2 hover:underline">{{ __('threads.archive_thread') }}</button>
                            </form>
                        @endif
                    @endcan

                    @can('delete', $thread)
                        <form method="POST" action="{{ route('threads.destroy', $thread) }}" onsubmit="return confirm(@js(__('threads.confirm_delete_thread')))">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-red-700 underline-offset-2 hover:underline">{{ __('threads.delete_thread') }}</button>
                        </form>
                    @endcan
                </div>
            </aside>
        @endcanany
    </div>
@endsection
