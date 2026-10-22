@extends('layouts.app')

@section('content')
    <a href="{{ route('users.index') }}" class="text-sm underline-offset-2 hover:underline">{{ __('users.back') }}</a>

    <h1 class="mt-3 text-2xl font-semibold">{{ $user->name }}</h1>
    <p class="mt-1 text-stone-600">{{ $user->email }}</p>
    <p class="mt-4 text-sm">{{ __('users.role') }}: {{ $user->role->label() }}</p>

    @if ($canChangeRole)
        <form method="POST" action="{{ route('users.role.update', $user) }}" class="mt-6 space-y-3 rounded-lg border border-stone-200 bg-white p-4">
            @csrf
            @method('PATCH')

            @if (count($assignableRoles) > 1)
                <label class="block text-sm font-medium" for="role">{{ __('users.role') }}</label>
                <select id="role" name="role" class="rounded-md border border-stone-300 bg-white px-3 py-2 text-sm">
                    @foreach ($assignableRoles as $role)
                        <option value="{{ $role->value }}" @selected($user->role === $role)>{{ $role->label() }}</option>
                    @endforeach
                </select>
                <div>
                    <button type="submit" class="rounded-md bg-stone-900 px-3 py-2 text-sm text-white">{{ __('users.save_role') }}</button>
                </div>
            @else
                <input type="hidden" name="role" value="{{ $assignableRoles[0]->value }}">
                <button type="submit" class="rounded-md bg-stone-900 px-3 py-2 text-sm text-white">{{ __('users.make_user') }}</button>
            @endif

            @error('role')
                <p class="text-sm text-red-700">{{ $message }}</p>
            @enderror
        </form>
    @endif

    @if ($canAssignFeatures)
        <section class="mt-6 rounded-lg border border-stone-200 bg-white p-4">
            <h2 class="text-lg font-medium">{{ __('users.permissions') }}</h2>

            @if ($features === [])
                <p class="mt-3 text-sm text-stone-600">{{ __('users.no_permissions') }}</p>
            @else
                <form method="POST" action="{{ route('users.features.update', $user) }}" class="mt-4 space-y-3">
                    @csrf
                    @method('PUT')

                    @foreach ($features as $feature)
                        <label class="flex items-center gap-2 text-sm">
                            <input type="hidden" name="features[{{ $feature->value }}]" value="0">
                            <input
                                type="checkbox"
                                name="features[{{ $feature->value }}]"
                                value="1"
                                class="rounded border-stone-300"
                                @checked($user->allows($feature))
                            >
                            {{ $feature->label() }}
                        </label>
                    @endforeach

                    @error('features')
                        <p class="text-sm text-red-700">{{ $message }}</p>
                    @enderror

                    <button type="submit" class="rounded-md bg-stone-900 px-3 py-2 text-sm text-white">{{ __('users.save_permissions') }}</button>
                </form>
            @endif
        </section>
    @endif
@endsection
