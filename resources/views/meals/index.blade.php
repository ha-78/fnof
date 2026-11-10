@extends('layouts.app')

@section('content')
    <div class="grid grid-cols-1 items-start gap-8 md:grid-cols-2">
        <h1 class="text-2xl font-semibold tracking-tight">{{ __('meals.meals') }}</h1>

        <div>
            @can('create', App\Models\Meal::class)
            <p class="mb-3 text-base text-muted">{{ __('meals.prompt') }}</p>

            <form method="POST" action="{{ route('meals.store') }}" class="meals-form space-y-3 rounded-3xl p-5">
                @csrf

                <label class="block text-sm font-medium" for="name">{{ __('meals.name') }}</label>
                <textarea id="name" name="name" rows="3" required maxlength="255" class="w-full rounded-2xl bg-field px-3 py-2 text-sm text-ink">{{ old('name') }}</textarea>

                @error('name')
                    <p class="rounded-2xl bg-danger-soft px-3 py-2 text-sm text-danger">{{ $message }}</p>
                @enderror

                <label class="block text-sm font-medium" for="recipe-link">{{ __('meals.recipe_link') }}</label>
                <input id="recipe-link" name="recipe_link" type="url" value="{{ old('recipe_link') }}" maxlength="2048" class="w-full rounded-2xl bg-field px-3 py-2 text-sm text-ink">

                @error('recipe_link')
                    <p class="rounded-2xl bg-danger-soft px-3 py-2 text-sm text-danger">{{ $message }}</p>
                @enderror

                <label class="block text-sm font-medium" for="comment">{{ __('meals.comment') }}</label>
                <textarea id="comment" name="comment" rows="4" maxlength="10000" class="w-full rounded-2xl bg-field px-3 py-2 text-sm text-ink">{{ old('comment') }}</textarea>

                @error('comment')
                    <p class="rounded-2xl bg-danger-soft px-3 py-2 text-sm text-danger">{{ $message }}</p>
                @enderror

                <div class="flex justify-end">
                    <button type="submit" class="rounded-full bg-[var(--color-f-5)] px-4 py-2 text-sm font-medium text-[var(--color-f-1)] hover:opacity-90">{{ __('meals.save') }}</button>
                </div>
            </form>
            @endcan

            @if ($meals->isEmpty())
                <p class="meals-card mt-8 rounded-3xl p-3 text-sm text-ink">{{ __('meals.empty') }}</p>
            @else
                <div class="mt-8 space-y-4">
                    @foreach ($meals as $meal)
                        <x-meal :meal="$meal" />
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
