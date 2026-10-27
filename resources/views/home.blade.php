@extends('layouts.app')

@section('content')
    <form method="POST" action="{{ route('threads.store') }}" class="space-y-3 rounded-3xl bg-form p-5">
        @csrf

        <label class="block text-sm font-medium" for="body">{{ __('threads.new') }}</label>
        <textarea id="body" name="body" rows="4" required maxlength="10000" class="w-full rounded-2xl bg-field px-3 py-2 text-sm text-ink">{{ old('body') }}</textarea>

        @error('body')
            <p class="rounded-2xl bg-danger-soft px-3 py-2 text-sm text-danger">{{ $message }}</p>
        @enderror

        <button type="submit" class="rounded-full bg-button px-4 py-2 text-sm font-medium text-button-ink hover:opacity-90">{{ __('threads.publish') }}</button>
    </form>

    <section class="mt-8" x-data="liveRegion" data-fragment="thread-list" data-version="{{ $version }}" data-version-url="{{ route('threads.version') }}">
        <div class="flex items-baseline justify-between gap-4">
            <h2 class="text-lg font-medium">{{ $listHeading }}</h2>
            <a href="{{ route('threads.index') }}" class="text-sm text-ink hover:underline">{{ __('threads.show_threads') }}</a>
        </div>

        <div x-ref="region">
            <x-thread-list :threads="$threads" :empty-message="$emptyMessage" />
        </div>
    </section>
@endsection
