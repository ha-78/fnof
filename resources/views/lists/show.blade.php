@extends('layouts.app')

@section('content')
    <div class="flex items-start justify-between gap-3">
        <h1 class="text-2xl font-semibold tracking-tight">{{ $list->name }}</h1>
        <a href="{{ route('lists.index') }}" class="shrink-0 text-sm text-ink hover:underline">{{ __('lists.lists') }}</a>
    </div>

    <x-list-items :items="$items" class="mt-8" />
@endsection
