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
                                                <x-icon name="globe" />
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
                                                    <x-icon name="trash" size="5" />
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
