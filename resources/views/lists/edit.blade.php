@extends('layouts.app')

@section('title', __('lists.edit'))

@section('content')
    <div class="space-y-6">
        <x-ui.quick-add :label="__('lists.new_list')" :placeholder="__('lists.name')" name="name" :action="route('lists.store')" :value="old('name')">
            @can('publish', App\Models\ItemList::class)
                <x-ui.switch :label="__('lists.is_public')" name="is_public" :checked="old('is_public')" icon="globe" />
            @endcan
        </x-ui.quick-add>

        @if ($shared->isEmpty() && $private->isEmpty())
            <p class="rounded-card border border-surface-line bg-surface px-4 py-3 text-[15px] text-muted">{{ __('lists.empty') }}</p>
        @endif

        @if ($shared->isNotEmpty())
            <x-ui.group :heading="__('lists.shared')">
                @foreach ($shared as $list)
                    <x-item-list :list="$list" />
                @endforeach
            </x-ui.group>
        @endif

        @if ($private->isNotEmpty())
            <x-ui.group :heading="__('lists.private')">
                @foreach ($private as $list)
                    <x-item-list :list="$list" />
                @endforeach
            </x-ui.group>
        @endif
    </div>
@endsection
