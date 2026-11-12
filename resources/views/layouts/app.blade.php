<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    data-section="{{ $section->color() }}"
    data-color="{{ $section->color() }}"
    data-posted-today="{{ __('threads.today') }}"
    data-posted-yesterday="{{ __('threads.yesterday') }}"
    data-posted-days-ago="{{ __('threads.days_ago_phrase') }}"
>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <x-theme-script />
    </head>
    <body class="min-h-screen bg-canvas text-ink antialiased">
        <x-site-header>
            <x-section-nav :current="$section" />
        </x-site-header>
        <main class="mx-auto max-w-5xl px-4 py-8">
            @if ($section->routeName() && ! $section->isMainPage())
                <x-section-back :section="$section" />
            @endif

            @if (session('status'))
                <p class="mb-6 rounded-3xl bg-status px-4 py-3 text-sm text-ink">{{ session('status') }}</p>
            @endif
            @yield('content')
        </main>
    </body>
</html>
