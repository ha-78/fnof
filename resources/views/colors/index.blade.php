<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ __('colors.title') }} — {{ config('app.name') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-canvas text-[var(--color-d-5)] antialiased">
        <main class="mx-auto max-w-5xl px-4 py-10">
            <h1 class="text-3xl font-semibold tracking-tight">{{ __('colors.title') }}</h1>
            <p class="mt-2 text-sm text-[var(--color-d-4)]">{{ __('colors.steps') }}</p>

            <div class="mt-10 space-y-10">
                @foreach ($families as $label => $name)
                    <section>
                        <h2 class="text-lg font-medium">{{ $label }}</h2>
                        <div class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-5">
                            @foreach ($steps as $step)
                                <figure>
                                    <div
                                        class="h-28 rounded-2xl border-2 border-[var(--color-d-4)]"
                                        style="background-color: var(--{{ $name }}-{{ $step }})"
                                    ></div>
                                    <figcaption class="mt-2 text-sm">
                                        <span class="font-medium">{{ $step }}</span>
                                        <span class="mt-0.5 block text-xs text-[var(--color-d-4)]">--{{ $name }}-{{ $step }}</span>
                                    </figcaption>
                                </figure>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>
        </main>
    </body>
</html>
