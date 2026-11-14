@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <section class="flex items-center gap-4 rounded-card border border-surface-line bg-surface p-4">
            <span aria-hidden="true" class="grid size-13 shrink-0 place-items-center rounded-full bg-tonal text-xl font-semibold text-tonal-ink">{{ Str::upper(Str::substr($user->name, 0, 1)) }}</span>
            <div class="min-w-0">
                <h2 class="truncate text-[17px] font-semibold">{{ $user->name }}</h2>
                <p class="truncate text-[13px] text-muted">{{ $user->email }}</p>
            </div>
        </section>

        <x-ui.group :heading="__('users.appearance')">
            <div x-data="themeToggle" class="space-y-2 px-4 py-3">
                <p class="text-[15px] font-medium">{{ __('themes.label') }}</p>
                <x-ui.segmented
                    :label="__('themes.label')"
                    :options="[
                        ['label' => __('themes.light'), 'when' => 'current === `light`', 'click' => 'pick(`light`)'],
                        ['label' => __('themes.dark'), 'when' => 'current === `dark`', 'click' => 'pick(`dark`)'],
                        ['label' => __('themes.default'), 'when' => 'current === `default`', 'click' => 'pick(`default`)'],
                    ]"
                />
            </div>
        </x-ui.group>

        @can('viewAny', App\Models\User::class)
            <x-ui.group :heading="__('users.household')">
                <a href="{{ route('users.index') }}" class="flex min-h-13 items-center gap-3 px-4 py-2 text-ink">
                    <span class="grid size-8 shrink-0 place-items-center rounded-field bg-tonal text-tonal-ink"><x-icon name="users" size="5" /></span>
                    <span class="min-w-0 flex-1">
                        <span class="block text-[15px] font-semibold">{{ __('users.users') }}</span>
                        <span class="block truncate text-[13px] text-muted">{{ __('users.users_hint') }}</span>
                    </span>
                    <span class="text-muted"><x-icon name="chevron-right" size="4" stroke="2" /></span>
                </a>
            </x-ui.group>
        @endcan

        <div class="space-y-2">
            <x-ui.group :heading="__('users.sessions')">
                @forelse ($devices as $device)
                    <div class="flex min-h-13 items-center gap-3 px-4 py-2">
                        <span class="grid size-8 shrink-0 place-items-center rounded-field bg-tonal text-tonal-ink"><x-icon name="device" size="5" /></span>
                        <span class="min-w-0 flex-1">
                            <span class="flex flex-wrap items-center gap-2">
                                <span class="text-[15px] font-semibold">{{ $device->device_name ?: __('users.devices.unknown') }}</span>
                                @if ($device->id === $currentDeviceId)
                                    <x-ui.badge>{{ __('users.devices.current') }}</x-ui.badge>
                                @endif
                            </span>
                            <span class="block text-[13px] text-muted">
                                {{ __('users.devices.last_used_at') }}
                                @if ($device->last_used_at)
                                    <time datetime="{{ $device->last_used_at->toAtomString() }}" data-posted>{{ $device->last_used_at->timezone(config('app.timezone'))->format('Y-m-d H:i') }}</time>
                                @else
                                    —
                                @endif
                            </span>
                        </span>
                    </div>
                @empty
                    <p class="px-4 py-3 text-[15px] text-muted">{{ __('users.devices.empty') }}</p>
                @endforelse

                <form method="POST" action="{{ route('logout') }}" data-color="{{ \App\Enums\Section::Calendar->color() }}">
                    @csrf
                    <button type="submit" class="flex min-h-13 w-full items-center gap-3 px-4 py-2 text-left text-accent">
                        <span class="grid size-8 shrink-0 place-items-center rounded-field bg-tonal text-tonal-ink"><x-icon name="logout" size="5" /></span>
                        <span class="text-[15px] font-semibold">{{ __('users.log_out') }}</span>
                    </button>
                </form>
            </x-ui.group>

            <p class="px-1 text-[13px] text-muted">{{ __('users.log_out_hint') }}</p>
        </div>
    </div>
@endsection
