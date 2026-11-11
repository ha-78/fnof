@extends('layouts.app')

@section('content')
    <div class="flex items-start justify-between gap-3">
        <h1 class="text-2xl font-semibold tracking-tight">{{ __('lists.lists') }}</h1>

        @can('create', App\Models\ItemList::class)
            <a href="{{ route('lists.edit') }}" class="shrink-0 text-sm text-ink hover:underline">{{ __('lists.edit') }}</a>
        @endcan
    </div>

    @if ($lists->isNotEmpty())
        @can('create', App\Models\ListItem::class)
        <form method="POST" action="{{ route('lists.items.store') }}" class="lists-form mt-8 space-y-3 rounded-3xl p-5">
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

            <div class="flex flex-col items-end gap-3 md:flex-row md:items-center md:justify-end">
                @can('publish', App\Models\ListItem::class)
                    <label class="flex items-center gap-2 self-start text-sm md:mr-auto">
                        <input type="checkbox" name="item_is_public" value="1" @checked(old('item_is_public'))>
                        {{ __('lists.is_public') }}
                    </label>
                @endcan

                <button type="submit" class="rounded-full bg-[var(--color-c-5)] px-4 py-2 text-sm font-medium text-[var(--color-c-1)] hover:opacity-90">{{ __('lists.save') }}</button>
            </div>
        </form>
        @endcan
    @endif

    @forelse ($lists as $list)
        <section class="mt-10">
            <div class="flex items-baseline justify-between gap-3">
                <h2 class="text-xl font-semibold tracking-tight">{{ $list->name }}</h2>
                <a href="{{ route('lists.show', $list) }}" class="shrink-0 text-sm text-ink hover:underline">{{ __('lists.handled_link') }}</a>
            </div>

            <div class="lists-surface mt-4 overflow-x-auto rounded-3xl" data-list-table>
                <table class="lists-table w-full text-left text-sm">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 text-center"><span class="sr-only">{{ __('lists.public') }}</span></th>
                            <th class="px-4 py-3 font-medium text-ink">{{ __('lists.name') }}</th>
                            <th class="px-4 py-3 font-medium text-ink">{{ __('lists.description') }}</th>
                            @if ($list->hasEstimatedItem())
                                <th class="px-4 py-3 font-medium text-ink">{{ __('lists.estimate') }}</th>
                            @endif
                            <th class="px-4 py-3 text-center"><span class="sr-only">{{ __('lists.handle_item') }}</span></th>
                            <th class="px-4 py-3"><span class="sr-only">{{ __('lists.delete_item') }}</span></th>
                        </tr>
                    </thead>
                    @if ($list->items->isNotEmpty())
                        <tbody>
                            @foreach ($list->items as $item)
                                <tr
                                    @can('handle', $item)
                                        class="transition-opacity duration-700"
                                        x-data="handleItem"
                                        data-handle-url="{{ route('lists.items.handle', $item) }}"
                                        :class="leaving && 'opacity-0'"
                                    @endcan
                                >
                                    <td class="px-4 py-3 text-center">
                                        @if ($item->is_public)
                                            <span class="inline-flex text-ink">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
                                                </svg>
                                                <span class="sr-only">{{ __('lists.public') }}</span>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 font-medium">{{ $item->name }}</td>
                                    <td class="px-4 py-3 whitespace-pre-wrap break-words">{{ $item->description }}</td>
                                    @if ($list->hasEstimatedItem())
                                        <td class="px-4 py-3 whitespace-nowrap tabular-nums">{{ $item->estimate }}</td>
                                    @endif
                                    <td class="px-4 py-3 text-center">
                                        @can('handle', $item)
                                            <input type="checkbox" class="size-5" aria-label="{{ __('lists.handle_item') }}" @change="mark($event)">
                                        @endcan
                                    </td>
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
                    @endif
                </table>

                <p @class(['px-4 py-3 text-sm text-ink', 'hidden' => $list->items->isNotEmpty()]) data-empty>{{ __('lists.items_empty') }}</p>
            </div>
        </section>
    @empty
        <p class="lists-surface mt-8 rounded-3xl px-4 py-3 text-sm text-ink">{{ __('lists.empty') }}</p>
    @endforelse
@endsection
