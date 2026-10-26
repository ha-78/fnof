@extends('layouts.app')

@section('content')
    <a href="{{ route('users.index') }}" class="inline-flex rounded-full bg-pill px-3 py-1 text-sm text-ink hover:bg-pill-active">{{ __('users.back') }}</a>

    <h1 class="mt-4 text-2xl font-semibold tracking-tight">{{ $user->name }}</h1>
    <p class="mt-2 text-muted">{{ $user->email }}</p>
    <p class="mt-4 inline-flex rounded-full bg-sun px-3 py-1 text-sm text-ink">{{ __('users.role') }}: {{ $user->role->label() }}</p>

    @if ($canChangeRole)
        <form method="POST" action="{{ route('users.role.update', $user) }}" class="mt-6 space-y-3 rounded-3xl bg-form p-5">
            @csrf
            @method('PATCH')

            @if (count($assignableRoles) > 1)
                <label class="block text-sm font-medium" for="role">{{ __('users.role') }}</label>
                <select id="role" name="role" class="rounded-2xl bg-field px-3 py-2 text-sm text-ink">
                    @foreach ($assignableRoles as $role)
                        <option value="{{ $role->value }}" @selected($user->role === $role)>{{ $role->label() }}</option>
                    @endforeach
                </select>
                <div>
                    <button type="submit" class="rounded-full bg-button px-4 py-2 text-sm font-medium text-button-ink hover:opacity-90">{{ __('users.save_role') }}</button>
                </div>
            @else
                <input type="hidden" name="role" value="{{ $assignableRoles[0]->value }}">
                <button type="submit" class="rounded-full bg-button px-4 py-2 text-sm font-medium text-button-ink hover:opacity-90">{{ __('users.make_user') }}</button>
            @endif

            @error('role')
                <p class="rounded-2xl bg-danger-soft px-3 py-2 text-sm text-danger">{{ $message }}</p>
            @enderror
        </form>
    @endif

    @if ($canAssignFeatures)
        <section class="mt-6 rounded-3xl bg-aside p-5">
            <h2 class="text-lg font-medium">{{ __('users.permissions') }}</h2>

            @if ($features === [])
                <p class="mt-3 text-sm text-muted">{{ __('users.no_permissions') }}</p>
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
                                @checked($user->allows($feature))
                            >
                            {{ $feature->label() }}
                        </label>
                    @endforeach

                    @error('features')
                        <p class="rounded-2xl bg-danger-soft px-3 py-2 text-sm text-danger">{{ $message }}</p>
                    @enderror

                    <button type="submit" class="rounded-full bg-button px-4 py-2 text-sm font-medium text-button-ink hover:opacity-90">{{ __('users.save_permissions') }}</button>
                </form>
            @endif
        </section>
    @endif
@endsection
