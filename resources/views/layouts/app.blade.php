<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-posted-today="{{ __('threads.today') }}" data-posted-yesterday="{{ __('threads.yesterday') }}" data-posted-days-ago="{{ __('threads.days_ago_phrase') }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <x-theme-script />
    </head>
    <body class="min-h-screen bg-canvas text-ink antialiased">
        <x-site-header :href="route('home')">
            @auth
                <nav class="flex flex-wrap items-center justify-end gap-2 text-sm">
                    <a href="{{ route('bookmarks.index') }}" class="inline-flex p-1 text-ink" aria-label="{{ __('bookmarks.bookmarks') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z" />
                        </svg>
                    </a>
                    <a href="{{ route('account') }}" class="inline-flex p-1 text-ink" aria-label="{{ __('users.account') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                    </a>
                </nav>
            @endauth
        </x-site-header>
        <main class="mx-auto max-w-5xl px-4 py-8">
            @if (session('status'))
                <p class="mb-6 rounded-3xl bg-status px-4 py-3 text-sm text-ink">{{ session('status') }}</p>
            @endif
            @yield('content')
        </main>
    </body>
</html>
