@extends('layouts.app')

@can('create', App\Models\Bookmark::class)
    @section('actions')
        <x-ui.sheet-trigger for="new-bookmark" variant="primary" icon :label="__('bookmarks.new')">
            <x-icon name="plus" size="5" stroke="1.8" />
        </x-ui.sheet-trigger>
    @endsection
@endcan

@section('content')
    @can('create', App\Models\Bookmark::class)
        <x-ui.sheet name="new-bookmark" :title="__('bookmarks.new')" :close="__('layout.close')" :open="$errors->any()">
            <form method="POST" action="{{ route('bookmarks.store') }}" class="space-y-3">
                @csrf

                <x-ui.field :label="__('bookmarks.name')" name="name" :value="old('name')" required maxlength="255" />
                <x-ui.field :label="__('bookmarks.url')" name="url" type="url" :value="old('url')" required maxlength="2048" placeholder="https://" />

                @can('publish', App\Models\Bookmark::class)
                    <x-ui.switch :label="__('bookmarks.is_public')" name="is_public" :checked="old('is_public')" icon="globe" />
                @endcan

                <div class="flex gap-2 pt-1">
                    <x-ui.button variant="tonal" class="flex-1" x-on:click="open = false">{{ __('bookmarks.cancel') }}</x-ui.button>
                    <x-ui.button type="submit" class="flex-2">{{ __('bookmarks.save') }}</x-ui.button>
                </div>
            </form>
        </x-ui.sheet>
    @endcan

    <div class="space-y-6">
        @if ($shared->isEmpty() && $private->isEmpty())
            <p class="rounded-card border border-surface-line bg-surface px-4 py-3 text-[15px] text-muted">{{ __('bookmarks.empty') }}</p>
        @endif

        @if ($shared->isNotEmpty())
            <x-ui.group :heading="__('bookmarks.shared')">
                @foreach ($shared as $bookmark)
                    <x-bookmark :bookmark="$bookmark" />
                @endforeach
            </x-ui.group>
        @endif

        @if ($private->isNotEmpty())
            <x-ui.group :heading="__('bookmarks.private')">
                @foreach ($private as $bookmark)
                    <x-bookmark :bookmark="$bookmark" />
                @endforeach
            </x-ui.group>
        @endif
    </div>
@endsection
