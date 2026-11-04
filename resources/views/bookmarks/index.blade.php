@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-semibold tracking-tight">{{ __('bookmarks.bookmarks') }}</h1>

    <form method="POST" action="{{ route('bookmarks.store') }}" class="mt-8 space-y-3 rounded-3xl bg-form p-5">
        @csrf

        <label class="block text-sm font-medium" for="name">{{ __('bookmarks.name') }}</label>
        <input id="name" name="name" type="text" value="{{ old('name') }}" required maxlength="255" class="w-full rounded-2xl bg-field px-3 py-2 text-sm text-ink">

        @error('name')
            <p class="rounded-2xl bg-danger-soft px-3 py-2 text-sm text-danger">{{ $message }}</p>
        @enderror

        <label class="block text-sm font-medium" for="url">{{ __('bookmarks.url') }}</label>
        <input id="url" name="url" type="url" value="{{ old('url') }}" required maxlength="2048" class="w-full rounded-2xl bg-field px-3 py-2 text-sm text-ink">

        @error('url')
            <p class="rounded-2xl bg-danger-soft px-3 py-2 text-sm text-danger">{{ $message }}</p>
        @enderror

        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_public" value="1" @checked(old('is_public'))>
            {{ __('bookmarks.is_public') }}
        </label>

        <button type="submit" class="rounded-full bg-button px-4 py-2 text-sm font-medium text-button-ink hover:opacity-90">{{ __('bookmarks.save') }}</button>
    </form>

    @if ($bookmarks->isEmpty())
        <p class="mt-8 rounded-3xl bg-lilac px-4 py-3 text-sm text-ink">{{ __('bookmarks.empty') }}</p>
    @else
        <div class="data-table mt-8 overflow-x-auto rounded-3xl">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr>
                        <th class="px-4 py-3 text-center font-medium text-ink">{{ __('bookmarks.public') }}</th>
                        <th class="px-4 py-3 font-medium text-ink">{{ __('bookmarks.name') }}</th>
                        <th class="px-4 py-3 font-medium text-ink">{{ __('bookmarks.url') }}</th>
                        <th class="px-4 py-3 font-medium text-ink">{{ __('bookmarks.created_by') }}</th>
                        <th class="px-4 py-3 font-medium text-ink">{{ __('bookmarks.created_at') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bookmarks as $bookmark)
                        <tr>
                            <td class="px-4 py-3 text-center">
                                @if ($bookmark->is_public)
                                    <span class="inline-flex text-ink">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 7.5A2.25 2.25 0 0 1 7.5 5.25h9a2.25 2.25 0 0 1 2.25 2.25v9a2.25 2.25 0 0 1-2.25 2.25h-9a2.25 2.25 0 0 1-2.25-2.25v-9Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.5 12 2.25 2.25 4.75-5" />
                                        </svg>
                                        <span class="sr-only">{{ __('bookmarks.public') }}</span>
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-medium">{{ $bookmark->name }}</td>
                            <td class="px-4 py-3 break-all">
                                <a href="{{ $bookmark->url }}" class="hover:underline" target="_blank" rel="noopener noreferrer">{{ $bookmark->url }}</a>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">{{ $bookmark->user->name }}</td>
                            <td class="px-4 py-3 whitespace-nowrap tabular-nums">
                                <time datetime="{{ $bookmark->created_at->timezone(config('app.timezone'))->toDateString() }}">{{ $bookmark->created_at->timezone(config('app.timezone'))->format('Y-m-d') }}</time>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
