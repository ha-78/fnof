@extends('layouts.app')

@can('create', App\Models\ItemList::class)
    @section('actions')
        <a href="{{ route('lists.edit') }}" class="inline-flex h-11 items-center rounded-field bg-tonal px-4 text-[15px] font-semibold text-tonal-ink">{{ __('lists.edit') }}</a>
    @endsection
@endcan

@section('content')
    @if ($lists->isEmpty())
        <p class="rounded-card border border-surface-line bg-surface px-4 py-3 text-[15px] text-muted">{{ __('lists.empty') }}</p>
    @else
        <div x-data="{ current: {{ (int) old('list_id', $selected) }}, publicLists: @json($publicListIds) }" class="space-y-4">
            <div role="group" aria-label="{{ __('lists.lists') }}" class="-mx-4 flex gap-1.5 overflow-x-auto px-4 pb-1">
                @foreach ($lists as $list)
                    <x-ui.chip
                        :selected="$list->id === $selected"
                        :when="'current === '.$list->id"
                        x-on:click="current = {{ $list->id }}; history.replaceState(null, '', '?list={{ $list->id }}')"
                    >
                        {{ $list->name }}
                        <span class="text-xs tabular-nums opacity-75">{{ $list->items->count() }}</span>
                    </x-ui.chip>
                @endforeach
            </div>

            @foreach ($lists as $list)
                <x-ui.group
                    :heading="$list->name"
                    data-list
                    x-show="current === {{ $list->id }}"
                    :x-cloak="$list->id !== $selected"
                >
                    <x-slot:badge>
                        @if ($list->is_public)
                            <x-ui.badge icon="globe">{{ __('lists.shared_one') }}</x-ui.badge>
                        @else
                            <x-ui.badge icon="lock">{{ __('lists.private_one') }}</x-ui.badge>
                        @endif
                    </x-slot:badge>

                    <x-slot:actions>
                        <a href="{{ route('lists.show', $list) }}" class="inline-flex items-center gap-0.5">{{ __('lists.handled_link') }}</a>
                    </x-slot:actions>

                    <p data-empty @class(['px-4 py-3 text-[15px] text-muted', 'hidden' => $list->items->isNotEmpty()])>{{ __('lists.items_empty') }}</p>

                    @foreach ($list->items as $item)
                        <x-list-item :item="$item" />
                    @endforeach
                </x-ui.group>
            @endforeach

            @can('create', App\Models\ListItem::class)
                @error('list_id')
                    <x-ui.message type="error" :dismissible="false">{{ $message }}</x-ui.message>
                @enderror

                <div class="sticky bottom-[calc(4.75rem+env(safe-area-inset-bottom))] z-10 -mx-4 bg-canvas px-4 py-2 sm:bottom-0 sm:mx-0 sm:px-0">
                    <x-ui.quick-add
                        :label="__('lists.add_item')"
                        :placeholder="__('lists.item_placeholder')"
                        name="item_name"
                        :action="route('lists.items.store')"
                        :value="old('item_name')"
                        expandable
                        :more-label="__('lists.more')"
                        :expanded="$errors->hasAny(['item_description', 'item_estimate', 'item_is_public']) || filled(old('item_description')) || filled(old('item_estimate')) || old('item_is_public')"
                    >
                        <x-slot:hidden>
                            <input type="hidden" name="list_id" value="{{ old('list_id', $selected) }}" x-bind:value="current">
                        </x-slot:hidden>

                        <x-ui.field :label="__('lists.description')" name="item_description" multiline maxlength="10000">{{ old('item_description') }}</x-ui.field>
                        <x-ui.field :label="__('lists.estimate')" name="item_estimate" type="number" step="1" inputmode="numeric" :value="old('item_estimate')" />

                        {{-- Items in a private list stay private: the switch is disabled (and not sent) there. --}}
                        @can('publish', App\Models\ListItem::class)
                            <fieldset x-show="publicLists.includes(current)" x-bind:disabled="! publicLists.includes(current)" @unless (in_array($selected, $publicListIds, true)) x-cloak @endunless>
                                <x-ui.switch :label="__('lists.is_public')" name="item_is_public" :checked="old('item_is_public')" icon="globe" />
                            </fieldset>
                        @endcan

                        <p x-show="! publicLists.includes(current)" @if (in_array($selected, $publicListIds, true)) x-cloak @endif class="flex min-h-11 items-center gap-2 rounded-field bg-surface px-3 text-[14px] text-muted">
                            <x-icon name="lock" size="5" class="shrink-0" />
                            {{ __('lists.private_list_info') }}
                        </p>

                        @error('item_is_public')
                            <p class="flex items-center gap-1.5 text-[13px] font-semibold text-field-error-ink">
                                <x-icon name="exclamation-circle" size="4" stroke="2" class="shrink-0" />
                                {{ $message }}
                            </p>
                        @enderror
                    </x-ui.quick-add>
                </div>
            @endcan
        </div>
    @endif
@endsection
