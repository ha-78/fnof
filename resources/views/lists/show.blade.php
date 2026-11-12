@extends('layouts.app')

@section('content')
    <div class="flex items-start justify-between gap-3">
        <h1 class="text-2xl font-semibold tracking-tight">{{ $list->name }}</h1>
    </div>

    <x-list-items :items="$items" class="mt-8" />
@endsection
