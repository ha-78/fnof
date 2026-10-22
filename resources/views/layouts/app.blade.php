<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <x-theme-script />
    </head>
    <body class="min-h-screen bg-stone-50 text-stone-900 antialiased">
        <header class="border-b border-stone-200 bg-white">
            <div class="mx-auto flex max-w-5xl items-center justify-between gap-4 px-4 py-4">
                <a href="{{ route('home') }}" class="font-semibold leading-snug">{{ __('layout.brand') }}</a>
                @auth
                    <nav class="flex items-center gap-4 text-sm">
                        <div x-data="themeToggle" class="flex items-center gap-1">
                            <span class="sr-only">{{ __('themes.label') }}</span>
                            @foreach (['default', 'light', 'dark', 'colorful'] as $theme)
                                <button
                                    type="button"
                                    class="rounded-md px-2 py-1"
                                    x-bind:class="current === '{{ $theme }}' ? 'bg-stone-900 text-white' : ''"
                                    x-on:click="pick('{{ $theme }}')"
                                >{{ __('themes.'.$theme) }}</button>
                            @endforeach
                        </div>
                        @can('viewAny', App\Models\User::class)
                            <a href="{{ route('users.index') }}" class="underline-offset-2 hover:underline">{{ __('users.users') }}</a>
                        @endcan
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="underline-offset-2 hover:underline">{{ __('users.log_out') }}</button>
                        </form>
                    </nav>
                @endauth
            </div>
        </header>
        <main class="mx-auto max-w-5xl px-4 py-8">
            @if (session('status'))
                <p class="mb-6 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('status') }}</p>
            @endif
            @yield('content')
        </main>
    </body>
</html>
