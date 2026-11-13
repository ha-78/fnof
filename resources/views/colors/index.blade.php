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
                @foreach ($families as $family)
                    <section>
                        <h2 class="text-lg font-medium">
                            <span class="font-semibold">{{ $family['letter'] }}</span>
                            {{ $family['name'] }}
                        </h2>
                        <p class="mt-1 text-sm text-[var(--color-d-4)]">{{ $family['used_for'] }}</p>
                        <div class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-5">
                            @foreach ($steps as $step)
                                <figure>
                                    <div
                                        class="h-28 rounded-2xl border-2 border-[var(--color-d-4)]"
                                        style="background-color: var(--{{ $family['token'] }}-{{ $step }})"
                                    ></div>
                                    <figcaption class="mt-2 text-sm">
                                        <span class="font-medium">{{ $family['letter'] }}{{ $step }}</span>
                                        <span class="mt-0.5 block text-xs text-[var(--color-d-4)]">--{{ $family['token'] }}-{{ $step }}</span>
                                    </figcaption>
                                </figure>
                            @endforeach
                        </div>

                        <div data-color="{{ $family['letter'] }}" class="mt-6 space-y-4 rounded-card border border-surface-line bg-surface p-4 text-ink">
                            <h3 class="text-[13px] font-semibold uppercase tracking-wide text-muted">{{ __('colors.primitives') }}</h3>

                            <div class="flex flex-wrap items-center gap-2">
                                <x-ui.button variant="primary">{{ __('colors.primary') }}</x-ui.button>
                                <x-ui.button variant="tonal">{{ __('colors.tonal') }}</x-ui.button>
                                <x-ui.button variant="ghost">{{ __('colors.ghost') }}</x-ui.button>
                                <x-ui.button variant="danger">{{ __('colors.danger') }}</x-ui.button>
                            </div>

                            <div class="space-y-2">
                                <x-ui.message>{{ __('colors.success') }}</x-ui.message>
                                <x-ui.message type="error">{{ __('colors.error') }}</x-ui.message>
                            </div>

                            <x-ui.field :label="__('colors.field')" :name="'name-'.$family['letter']" />
                            <x-ui.field :label="__('colors.note')" :name="'note-'.$family['letter']" multiline />

                            <x-ui.quick-add :label="__('colors.add')" :name="'add-'.$family['letter']">
                                <x-ui.field :label="__('colors.note')" :name="'extra-'.$family['letter']" />
                            </x-ui.quick-add>

                            <x-ui.group :heading="__('colors.group')">
                                <x-ui.row :title="__('colors.row')" :meta="__('colors.meta')">
                                    <x-icon name="globe" size="5" class="text-accent" />
                                </x-ui.row>
                            </x-ui.group>

                            <x-ui.segmented
                                :label="__('colors.segmented')"
                                :options="[
                                    ['label' => __('colors.open'), 'selected' => true],
                                    ['label' => __('colors.archive'), 'selected' => false],
                                ]"
                            />

                            <div class="flex flex-wrap gap-2">
                                <x-ui.chip selected>{{ __('colors.chip') }}</x-ui.chip>
                                <x-ui.chip>{{ __('colors.chip_idle') }}</x-ui.chip>
                            </div>

                            <x-ui.sheet :title="__('colors.sheet')" :close="__('colors.close')">
                                <x-slot:trigger>{{ __('colors.open_sheet') }}</x-slot:trigger>
                                <p class="text-[15px]">{{ __('colors.sheet_body') }}</p>
                            </x-ui.sheet>
                        </div>
                    </section>
                @endforeach
            </div>
        </main>
    </body>
</html>
