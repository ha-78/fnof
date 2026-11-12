@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-semibold tracking-tight">{{ __('bookmarks.bookmarks') }}</h1>

    @can('create', App\Models\Bookmark::class)
    <form method="POST" action="{{ route('bookmarks.store') }}" class="bookmarks-form mt-8 space-y-3 rounded-3xl p-5">
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

        <div class="flex flex-col items-end gap-3 md:flex-row md:items-center md:justify-end">
            @can('publish', App\Models\Bookmark::class)
                <label class="flex items-center gap-2 self-start text-sm md:mr-auto">
                    <input type="checkbox" name="is_public" value="1" @checked(old('is_public'))>
                    {{ __('bookmarks.is_public') }}
                </label>
            @endcan

            <button type="submit" class="rounded-full bg-[var(--color-e-5)] px-4 py-2 text-sm font-medium text-[var(--color-e-1)] hover:opacity-90">{{ __('bookmarks.save') }}</button>
        </div>
    </form>
    @endcan

    @if ($bookmarks->isEmpty())
        <p class="bookmarks-surface mt-8 rounded-3xl px-4 py-3 text-sm text-ink">{{ __('bookmarks.empty') }}</p>
    @else
        <div class="bookmarks-surface mt-8 overflow-x-auto rounded-3xl">
            <table class="bookmarks-table w-full text-left text-sm">
                <thead>
                    <tr>
                        <th class="px-4 py-3 text-center font-medium text-ink">{{ __('bookmarks.public') }}</th>
                        <th class="px-4 py-3 font-medium text-ink">{{ __('bookmarks.name') }}</th>
                        <th class="px-4 py-3 font-medium text-ink">{{ __('bookmarks.url') }}</th>
                        <th class="px-4 py-3 font-medium text-ink">{{ __('bookmarks.created_by') }}</th>
                        <th class="px-4 py-3 font-medium text-ink">{{ __('bookmarks.created_at') }}</th>
                        <th class="px-4 py-3"><span class="sr-only">{{ __('bookmarks.delete') }}</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bookmarks as $bookmark)
                        <tr>
                            <td class="px-4 py-3 text-center">
                                @if ($bookmark->is_public)
                                    <span class="inline-flex text-ink">
                                        <x-icon name="globe" />
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
                            <td class="px-4 py-3 text-right">
                                @can('delete', $bookmark)
                                    <form method="POST" action="{{ route('bookmarks.destroy', $bookmark) }}" onsubmit="return confirm(@js(__('bookmarks.confirm_delete')))">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex p-1 text-ink" aria-label="{{ __('bookmarks.delete') }}">
                                            <x-icon name="trash" size="5" />
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
