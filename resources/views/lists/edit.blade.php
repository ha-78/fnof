@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-semibold tracking-tight">{{ __('lists.edit') }}</h1>

    <form method="POST" action="{{ route('lists.store') }}" class="lists-form mt-8 space-y-3 rounded-3xl p-5">
        @csrf

        <label class="block text-sm font-medium" for="name">{{ __('lists.name') }}</label>
        <input id="name" name="name" type="text" value="{{ old('name') }}" required maxlength="255" class="w-full rounded-2xl bg-field px-3 py-2 text-sm text-ink">

        @error('name')
            <p class="rounded-2xl bg-danger-soft px-3 py-2 text-sm text-danger">{{ $message }}</p>
        @enderror

        <div class="flex flex-col items-end gap-3 md:flex-row md:items-center md:justify-end">
            @can('publish', App\Models\ItemList::class)
                <label class="flex items-center gap-2 self-start text-sm md:mr-auto">
                    <input type="checkbox" name="is_public" value="1" @checked(old('is_public'))>
                    {{ __('lists.is_public') }}
                </label>
            @endcan

            <button type="submit" class="rounded-full bg-[var(--color-c-5)] px-4 py-2 text-sm font-medium text-[var(--color-c-1)] hover:opacity-90">{{ __('lists.save') }}</button>
        </div>
    </form>

    @if ($lists->isEmpty())
        <p class="lists-surface mt-8 rounded-3xl px-4 py-3 text-sm text-ink">{{ __('lists.empty') }}</p>
    @else
        <div class="lists-surface mt-8 overflow-x-auto rounded-3xl">
            <table class="lists-table w-full text-left text-sm">
                <thead>
                    <tr>
                        <th class="px-4 py-3 text-center font-medium text-ink">{{ __('lists.public') }}</th>
                        <th class="px-4 py-3 font-medium text-ink">{{ __('lists.name') }}</th>
                        <th class="px-4 py-3 font-medium text-ink">{{ __('lists.created_by') }}</th>
                        <th class="px-4 py-3 font-medium text-ink">{{ __('lists.created_at') }}</th>
                        <th class="px-4 py-3"><span class="sr-only">{{ __('lists.delete') }}</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lists as $list)
                        <tr>
                            <td class="px-4 py-3 text-center">
                                @if ($list->is_public)
                                    <span class="inline-flex text-ink">
                                        <x-icon name="globe" />
                                        <span class="sr-only">{{ __('lists.public') }}</span>
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-medium">{{ $list->name }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">{{ $list->user->name }}</td>
                            <td class="px-4 py-3 whitespace-nowrap tabular-nums">
                                <time datetime="{{ $list->created_at->timezone(config('app.timezone'))->toDateString() }}">{{ $list->created_at->timezone(config('app.timezone'))->format('Y-m-d') }}</time>
                            </td>
                            <td class="px-4 py-3 text-right">
                                @can('delete', $list)
                                    <form method="POST" action="{{ route('lists.destroy', $list) }}" onsubmit="return confirm(@js(__('lists.confirm_delete')))">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex p-1 text-ink" aria-label="{{ __('lists.delete') }}">
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
