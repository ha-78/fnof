@extends('layouts.app')

@section('content')
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <h1 class="text-2xl font-semibold tracking-tight">{{ $user->name }}</h1>
            <p class="mt-2 text-muted">{{ $user->email }}</p>
        </div>

        @can('viewAny', App\Models\User::class)
            <a href="{{ route('users.index') }}" class="shrink-0 text-sm text-ink hover:underline">{{ __('users.users') }}</a>
        @endcan
    </div>

    <section class="mt-10">
        <h2 class="text-lg font-medium">{{ __('themes.label') }}</h2>
        <div x-data="themeToggle" class="mt-4 flex flex-wrap items-center gap-1 text-sm">
            @foreach (['light', 'dark', 'default'] as $theme)
                <button
                    type="button"
                    class="cursor-pointer px-2 py-1 text-ink hover:underline"
                    x-bind:class="current === '{{ $theme }}' ? 'font-semibold' : ''"
                    x-on:click="pick('{{ $theme }}')"
                >{{ __('themes.'.$theme) }}</button>
            @endforeach
        </div>
    </section>

    <section class="mt-10">
        <h2 class="text-lg font-medium">{{ __('users.sessions') }}</h2>

        @if ($devices->isEmpty())
            <p class="mt-4 text-sm text-muted">{{ __('users.devices.empty') }}</p>
        @else
            <div class="data-table mt-4 overflow-x-auto rounded-3xl">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 font-medium text-ink">{{ __('users.devices.name') }}</th>
                            <th class="px-4 py-3 font-medium text-ink">{{ __('users.devices.last_used_at') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($devices as $device)
                            <tr>
                                <td class="px-4 py-3">
                                    <span class="inline-flex flex-wrap items-center gap-2">
                                        <span>{{ $device->device_name ?: __('users.devices.unknown') }}</span>
                                        @if ($device->id === $currentDeviceId)
                                            <span class="inline-flex shrink-0 rounded-full bg-pill px-2 py-0.5 text-xs text-ink">{{ __('users.devices.current') }}</span>
                                        @endif
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 tabular-nums">
                                    @if ($device->last_used_at)
                                        <time datetime="{{ $device->last_used_at->toAtomString() }}" data-posted>{{ $device->last_used_at->timezone(config('app.timezone'))->format('Y-m-d H:i') }}</time>
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <form method="POST" action="{{ route('logout') }}" class="mt-4">
            @csrf
            <button type="submit" class="rounded-full bg-pill px-3 py-1 text-sm text-ink hover:bg-pill-active">{{ __('users.log_out') }}</button>
            <p class="mt-3 text-sm text-muted">{{ __('users.log_out_hint') }}</p>
        </form>
    </section>
@endsection
