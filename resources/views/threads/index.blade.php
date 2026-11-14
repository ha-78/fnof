@extends('layouts.app')

@section('content')
    <div class="space-y-4">
        <x-ui.segmented
            :label="__('threads.threads')"
            :options="[
                ['label' => __('threads.open'), 'href' => route('threads.index'), 'selected' => ! $showingArchived],
                ['label' => __('threads.archive'), 'href' => route('threads.index', ['archived' => 1]), 'selected' => $showingArchived],
            ]"
        />

        @unless ($showingArchived)
            @can('create', App\Models\Thread::class)
                <x-ui.quick-add
                    :label="__('threads.new_thread')"
                    :placeholder="__('threads.new')"
                    name="body"
                    :action="route('threads.store')"
                    :value="old('body')"
                    multiline
                    required
                    maxlength="10000"
                />
            @endcan
        @endunless

        <section x-data="liveRegion" data-fragment="thread-list" data-version="{{ $version }}" data-version-url="{{ route('threads.version') }}">
            <h2 class="sr-only">{{ $listHeading }}</h2>

            <div x-ref="region">
                <x-thread-list :threads="$threads" :empty-message="$emptyMessage" />
            </div>
        </section>
    </div>
@endsection
