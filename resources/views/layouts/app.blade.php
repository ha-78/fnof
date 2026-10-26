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
                        <a href="{{ route('users.index') }}" class="inline-flex rounded-md p-1 hover:bg-stone-100" aria-label="{{ __('users.account') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                        </a>
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
