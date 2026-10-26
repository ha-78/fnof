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
    <body class="min-h-screen bg-canvas text-ink antialiased">
        <x-site-header :href="url('/')" />
        <main class="mx-auto max-w-5xl px-4 py-8">
            @yield('content')
        </main>
    </body>
</html>
