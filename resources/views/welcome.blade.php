@extends('layouts.front')

@section('header')
    <a
        href="{{ route('home') }}"
        data-color="{{ \App\Enums\Section::Start->color() }}"
        class="grid size-9 place-items-center rounded-full bg-tonal text-ink"
        aria-label="{{ __('home.title') }}"
    >
        <x-section-icon :section="\App\Enums\Section::Start" />
    </a>
@endsection

@section('content')
    <div class="mx-auto max-w-3xl space-y-8">
        <div class="space-y-3 text-center">
            <h1 class="text-2xl font-semibold tracking-tight">{{ __('home.welcome') }}</h1>
            <p class="mx-auto max-w-xl text-[15px] leading-6 text-muted">{{ __('welcome.intro') }}</p>

            @guest
                <a href="{{ route('login') }}" class="inline-flex h-11 items-center rounded-field bg-primary px-5 text-[15px] font-semibold text-primary-ink">{{ __('auth_pages.login') }}</a>
            @endguest
        </div>

        {{-- One card per part of the app, each in its section color (see .cursor/rules/feature-sync.mdc). --}}
        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
            <x-welcome-card :section="\App\Enums\Section::Start" :color="\App\Enums\Section::Start->color()" :title="__('home.title')">{{ __('welcome.home_body') }}</x-welcome-card>
            <x-welcome-card icon="calendar" :color="\App\Enums\Section::Calendar->color()" :title="__('welcome.calendar_title')">{{ __('welcome.calendar_body') }}</x-welcome-card>
            <x-welcome-card :section="\App\Enums\Section::Threads" :color="\App\Enums\Section::Threads->color()" :title="__('threads.threads')">{{ __('welcome.threads_body') }}</x-welcome-card>
            <x-welcome-card :section="\App\Enums\Section::Lists" :color="\App\Enums\Section::Lists->color()" :title="__('lists.lists')">{{ __('welcome.lists_body') }}</x-welcome-card>
            <x-welcome-card :section="\App\Enums\Section::Bookmarks" :color="\App\Enums\Section::Bookmarks->color()" :title="__('bookmarks.bookmarks')">{{ __('welcome.bookmarks_body') }}</x-welcome-card>
            <x-welcome-card :section="\App\Enums\Section::Meals" :color="\App\Enums\Section::Meals->color()" :title="__('meals.meals')">{{ __('welcome.meals_body') }}</x-welcome-card>
            <x-welcome-card :section="\App\Enums\Section::Personal" :color="\App\Enums\Section::Personal->color()" :title="__('users.account')" class="md:col-span-2 md:mx-auto md:w-[calc(50%-0.375rem)]">{{ __('welcome.account_body') }}</x-welcome-card>
        </div>
    </div>
@endsection
