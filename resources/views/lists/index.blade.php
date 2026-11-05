@extends('layouts.app')

@section('content')
    <div class="flex items-start justify-between gap-3">
        <h1 class="text-2xl font-semibold tracking-tight">{{ __('lists.lists') }}</h1>

        @can('manage', App\Models\ItemList::class)
            <a href="{{ route('lists.edit') }}" class="shrink-0 text-sm text-ink hover:underline">{{ __('lists.edit') }}</a>
        @endcan
    </div>

    <h2 class="mt-10 text-xl font-semibold tracking-tight">{{ __('lists.items') }}</h2>

    @if ($lists->isNotEmpty())
        <form method="POST" action="{{ route('lists.items.store') }}" class="mt-8 space-y-3 rounded-3xl bg-form p-5">
            @csrf

            <label class="block text-sm font-medium" for="item-list">{{ __('lists.list') }}</label>
            <select id="item-list" name="list_id" required class="w-full rounded-2xl bg-field px-3 py-2 text-sm text-ink">
                @foreach ($lists as $list)
                    <option value="{{ $list->id }}" @selected((string) old('list_id') === (string) $list->id)>{{ $list->name }}</option>
                @endforeach
            </select>

            @error('list_id')
                <p class="rounded-2xl bg-danger-soft px-3 py-2 text-sm text-danger">{{ $message }}</p>
            @enderror

            <label class="block text-sm font-medium" for="item-name">{{ __('lists.name') }}</label>
            <input id="item-name" name="item_name" type="text" value="{{ old('item_name') }}" required maxlength="255" class="w-full rounded-2xl bg-field px-3 py-2 text-sm text-ink">

            @error('item_name')
                <p class="rounded-2xl bg-danger-soft px-3 py-2 text-sm text-danger">{{ $message }}</p>
            @enderror

            <label class="block text-sm font-medium" for="item-description">{{ __('lists.description') }}</label>
            <textarea id="item-description" name="item_description" rows="3" maxlength="10000" class="w-full rounded-2xl bg-field px-3 py-2 text-sm text-ink">{{ old('item_description') }}</textarea>

            @error('item_description')
                <p class="rounded-2xl bg-danger-soft px-3 py-2 text-sm text-danger">{{ $message }}</p>
            @enderror

            <label class="block text-sm font-medium" for="item-estimate">{{ __('lists.estimate') }}</label>
            <input id="item-estimate" name="item_estimate" type="number" value="{{ old('item_estimate') }}" step="1" class="w-full rounded-2xl bg-field px-3 py-2 text-sm text-ink">

            @error('item_estimate')
                <p class="rounded-2xl bg-danger-soft px-3 py-2 text-sm text-danger">{{ $message }}</p>
            @enderror

            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="item_is_public" value="1" @checked(old('item_is_public', true))>
                {{ __('lists.is_public') }}
            </label>

            <button type="submit" class="rounded-full bg-button px-4 py-2 text-sm font-medium text-button-ink hover:opacity-90">{{ __('lists.save') }}</button>
        </form>
    @endif

    @if ($items->isEmpty())
        <p class="mt-8 rounded-3xl bg-lilac px-4 py-3 text-sm text-ink">{{ __('lists.items_empty') }}</p>
    @else
        <div class="data-table mt-8 overflow-x-auto rounded-3xl">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr>
                        <th class="px-4 py-3 text-center font-medium text-ink">{{ __('lists.public') }}</th>
                        <th class="px-4 py-3 font-medium text-ink">{{ __('lists.list') }}</th>
                        <th class="px-4 py-3 font-medium text-ink">{{ __('lists.name') }}</th>
                        <th class="px-4 py-3 font-medium text-ink">{{ __('lists.description') }}</th>
                        <th class="px-4 py-3 font-medium text-ink">{{ __('lists.estimate') }}</th>
                        <th class="px-4 py-3"><span class="sr-only">{{ __('lists.delete_item') }}</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        <tr>
                            <td class="px-4 py-3 text-center">
                                @if ($item->is_public)
                                    <span class="inline-flex text-ink">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 7.5A2.25 2.25 0 0 1 7.5 5.25h9a2.25 2.25 0 0 1 2.25 2.25v9a2.25 2.25 0 0 1-2.25 2.25h-9a2.25 2.25 0 0 1-2.25-2.25v-9Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.5 12 2.25 2.25 4.75-5" />
                                        </svg>
                                        <span class="sr-only">{{ __('lists.public') }}</span>
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">{{ $item->list->name }}</td>
                            <td class="px-4 py-3 font-medium">{{ $item->name }}</td>
                            <td class="px-4 py-3 whitespace-pre-wrap break-words">{{ $item->description }}</td>
                            <td class="px-4 py-3 whitespace-nowrap tabular-nums">{{ $item->estimate }}</td>
                            <td class="px-4 py-3 text-right">
                                @can('delete', $item)
                                    <form method="POST" action="{{ route('lists.items.destroy', $item) }}" onsubmit="return confirm(@js(__('lists.confirm_delete_item')))">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex p-1 text-ink" aria-label="{{ __('lists.delete_item') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
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
