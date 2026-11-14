@extends('layouts.app')

@section('title', filled($thread->title) ? $thread->title : __('threads.thread'))

@canany(['update', 'archive', 'delete'], $thread)
    @section('actions')
        <x-ui.sheet-trigger for="manage-thread" icon :label="__('threads.manage')">
            <x-icon name="dots" size="5" stroke="2" />
        </x-ui.sheet-trigger>
    @endsection
@endcanany

@section('content')
    <div
        x-data="liveRegion"
        data-fragment="replies"
        data-archived="{{ $thread->isArchived() ? '1' : '0' }}"
        data-missing="{{ route('threads.index') }}"
        data-version="{{ $version }}"
        data-version-url="{{ route('threads.version') }}"
        class="space-y-4"
    >
        @canany(['update', 'archive', 'delete'], $thread)
            <x-ui.sheet name="manage-thread" :title="__('threads.manage')" :close="__('layout.close')" :open="$errors->has('title')">
                <div class="space-y-4">
                    @can('update', $thread)
                        <form method="POST" action="{{ route('threads.update', $thread) }}" class="space-y-2">
                            @csrf
                            @method('PATCH')

                            <x-ui.field :label="__('threads.name')" name="title" :value="old('title', $thread->title)" maxlength="255" />
                            <x-ui.button type="submit" variant="tonal" class="w-full">{{ __('threads.save_name') }}</x-ui.button>
                        </form>
                    @endcan

                    @can('archive', $thread)
                        <form method="POST" action="{{ route('threads.unarchive', $thread) }}" x-show="archived" @unless ($thread->isArchived()) x-cloak @endunless>
                            @csrf
                            @method('PATCH')
                            <x-ui.button type="submit" variant="tonal" class="w-full"><x-icon name="archive" size="5" />{{ __('threads.unarchive') }}</x-ui.button>
                        </form>
                        <form method="POST" action="{{ route('threads.archive', $thread) }}" x-show="! archived" @if ($thread->isArchived()) x-cloak @endif>
                            @csrf
                            @method('PATCH')
                            <x-ui.button type="submit" variant="tonal" class="w-full"><x-icon name="archive" size="5" />{{ __('threads.archive_thread') }}</x-ui.button>
                        </form>
                    @endcan

                    @can('delete', $thread)
                        <form method="POST" action="{{ route('threads.destroy', $thread) }}" onsubmit="return confirm(@js(__('threads.confirm_delete_thread')))">
                            @csrf
                            @method('DELETE')
                            <x-ui.button type="submit" variant="danger" class="w-full"><x-icon name="trash" size="5" />{{ __('threads.delete_thread') }}</x-ui.button>
                        </form>
                    @endcan
                </div>
            </x-ui.sheet>
        @endcanany

        <x-message :author-id="$thread->user_id" :body="$thread->body" :author="$thread->user?->name" :posted-at="$thread->created_at" />

        <div x-ref="region">
            <x-replies :thread="$thread" />
        </div>

        @can('reply', $thread)
            <div class="sticky bottom-[calc(4.75rem+env(safe-area-inset-bottom))] z-10 -mx-4 bg-canvas px-4 py-2 sm:bottom-0 sm:mx-0 sm:px-0" x-show="! archived" @if ($thread->isArchived()) x-cloak @endif>
                <x-ui.quick-add
                    :label="__('threads.reply')"
                    :placeholder="__('threads.reply_placeholder')"
                    name="body"
                    :action="route('threads.replies.store', $thread)"
                    :value="old('body')"
                    multiline
                    required
                    maxlength="10000"
                />
            </div>
        @endcan
    </div>
@endsection
