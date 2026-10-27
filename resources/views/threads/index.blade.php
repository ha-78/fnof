@extends('layouts.app')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        @if ($showingArchived)
            <a href="{{ route('threads.index') }}" class="inline-flex rounded-full bg-pill px-3 py-1 text-sm text-ink hover:bg-pill-active">{{ __('threads.open') }}</a>
        @else
            <a href="{{ route('threads.index', ['archived' => 1]) }}" class="inline-flex rounded-full bg-pill px-3 py-1 text-sm text-ink hover:bg-pill-active">{{ __('threads.archive') }}</a>
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

    <section class="mt-8" x-data="liveRegion" data-fragment="thread-list" data-version="{{ $version }}" data-version-url="{{ route('threads.version') }}">
        <h2 class="text-lg font-medium">{{ $listHeading }}</h2>

        <div x-ref="region">
            <x-thread-list :threads="$threads" :empty-message="$emptyMessage" />
        </div>
    </section>
@endsection
