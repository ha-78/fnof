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
            @if ($section->routeName() && ! $section->isMainPage())
                <x-slot:leading>
                    <x-section-back :section="$section" />
                </x-slot:leading>
            @endif
            <x-slot:title>
                @hasSection('title')
                    @yield('title')
                @else
                    {{ $section->label() }}
                @endif
            </x-slot:title>
            <div class="flex items-center gap-2">
                @hasSection('actions')
                    <div class="flex items-center gap-2 sm:hidden">@yield('actions')</div>
                @endif
                <x-section-nav variant="top" :current="$section" />
            </div>
        </x-site-header>
        <x-section-nav variant="tabs" :current="$section" />
        <main class="mx-auto max-w-5xl px-4 pt-8 pb-24 sm:pb-8">
            @hasSection('actions')
                <div class="-mt-4 mb-4 hidden items-center justify-end gap-2 sm:flex">@yield('actions')</div>
            @endif
            @if (session('status'))
                <x-ui.message class="mb-6">{{ session('status') }}</x-ui.message>
            @endif
            @if (session('error'))
                <x-ui.message type="error" class="mb-6">{{ session('error') }}</x-ui.message>
            @endif
            @yield('content')
        </main>
    </body>
</html>
