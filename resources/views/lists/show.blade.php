@extends('layouts.app')

@section('title', $list->name)

@section('content')
    <div class="space-y-6">
        @if ($open->isEmpty() && $handled->isEmpty())
            <p class="rounded-card border border-surface-line bg-surface px-4 py-3 text-[15px] text-muted">{{ __('lists.items_empty') }}</p>
        @endif

        @if ($open->isNotEmpty())
            {{-- Checking an item reloads the page so it moves to the handled group with name and date. --}}
            <x-ui.group :heading="__('lists.open')" data-list data-reload-on-handle>
                @foreach ($open as $item)
                    <x-list-item :item="$item" />
                @endforeach
            </x-ui.group>
        @endif

        @if ($handled->isNotEmpty())
            <x-ui.group :heading="__('lists.handled')" data-list data-reload-on-handle>
                @foreach ($handled as $item)
                    <x-list-item-summary :item="$item" />
                @endforeach
            </x-ui.group>
        @endif
    </div>
@endsection
