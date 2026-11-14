@extends('layouts.app')

@section('title', $user->name)
@section('back', route('users.index'))

@section('content')
    <div class="space-y-6">
        <section class="flex items-center gap-4 rounded-card border border-surface-line bg-surface p-4">
            <span aria-hidden="true" class="grid size-13 shrink-0 place-items-center rounded-full bg-tonal text-xl font-semibold text-tonal-ink">{{ Str::upper(Str::substr($user->name, 0, 1)) }}</span>
            <div class="min-w-0 flex-1">
                <h2 class="truncate text-[17px] font-semibold">{{ $user->name }}</h2>
                <p class="truncate text-[13px] text-muted">{{ $user->email }}</p>
            </div>
            <x-ui.badge class="shrink-0">{{ __('users.role') }}: {{ $user->role->label() }}</x-ui.badge>
        </section>

        @if ($canChangeRole)
            <x-ui.form-card :label="__('users.role')" :action="route('users.role.update', $user)" method="PATCH">
                @if (count($assignableRoles) > 1)
                    <x-ui.field
                        :label="__('users.role')"
                        name="role"
                        :options="collect($assignableRoles)->mapWithKeys(fn ($role) => [$role->value => $role->label()])->all()"
                        :selected="$user->role->value"
                    />
                    <div class="flex justify-end">
                        <x-ui.button type="submit" variant="tonal">{{ __('users.save_role') }}</x-ui.button>
                    </div>
                @else
                    <input type="hidden" name="role" value="{{ $assignableRoles[0]->value }}">
                    <x-ui.button type="submit" class="w-full">{{ __('users.make_member') }}</x-ui.button>

                    @error('role')
                        <p class="flex items-center gap-1.5 text-[13px] font-semibold text-field-error-ink">
                            <x-icon name="exclamation-circle" size="4" stroke="2" class="shrink-0" />
                            {{ $message }}
                        </p>
                    @enderror
                @endif
            </x-ui.form-card>
        @endif

        @if ($canAssignFeatures)
            <section class="space-y-3">
                <h2 class="px-1 text-[13px] font-semibold uppercase tracking-wide text-muted">{{ __('users.permissions') }}</h2>

                @if ($featureGroups === [])
                    <p class="rounded-card border border-surface-line bg-surface px-4 py-3 text-[15px] text-muted">{{ __('users.no_permissions') }}</p>
                @else
                    <form method="POST" action="{{ route('users.features.update', $user) }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        {{-- Each group takes its section's colors, so the switches read as that part of the app. --}}
                        @foreach ($featureGroups as $group)
                            <x-ui.group :heading="$group['label']" data-color="{{ $group['section']->color() }}">
                                @foreach ($group['features'] as $feature)
                                    <div>
                                        <input type="hidden" name="features[{{ $feature->value }}]" value="0">
                                        <x-ui.switch plain :label="$feature->label()" name="features[{{ $feature->value }}]" :checked="$user->allows($feature)" />
                                    </div>
                                @endforeach
                            </x-ui.group>
                        @endforeach

                        @error('features')
                            <x-ui.message type="error" :dismissible="false">{{ $message }}</x-ui.message>
                        @enderror

                        <x-ui.button type="submit" class="w-full">{{ __('users.save_permissions') }}</x-ui.button>
                    </form>
                @endif
            </section>
        @endif
    </div>
@endsection
