@extends('layouts.front')

@section('header')
    <a
        href="{{ route('home') }}"
        class="inline-flex rounded-full p-1.5 text-[var(--color-d-5)]"
        style="background-color: var(--color-d-2)"
        aria-label="{{ __('home.title') }}"
    >
        <x-section-icon :section="\App\Enums\Section::Start" />
    </a>
@endsection

@section('content')
    <div class="mx-auto max-w-3xl">
        @guest
            <div class="flex justify-end">
                <a href="{{ route('login') }}" class="text-sm text-ink hover:underline">{{ __('auth_pages.login') }}</a>
            </div>
        @endguest

        <h1 class="mt-8 text-center text-2xl font-semibold tracking-tight">{{ __('home.welcome') }}</h1>
        <p class="mx-auto mt-3 max-w-xl text-center text-sm leading-6 text-muted">{{ __('welcome.intro') }}</p>

        <div class="mt-10 grid grid-cols-1 gap-4 md:grid-cols-2">
            <article class="welcome-card rounded-3xl p-5" data-color="d">
                <h2 class="text-lg font-semibold">{{ __('home.title') }}</h2>
                <p class="mt-3 text-sm leading-6">{{ __('welcome.home_body') }}</p>
            </article>

            <article class="welcome-card rounded-3xl p-5" data-color="b">
                <h2 class="text-lg font-semibold">{{ __('welcome.calendar_title') }}</h2>
                <p class="mt-3 text-sm leading-6">{{ __('welcome.calendar_body') }}</p>
            </article>

            <article class="welcome-card rounded-3xl p-5" data-color="a">
                <h2 class="text-lg font-semibold">{{ __('threads.threads') }}</h2>
                <p class="mt-3 text-sm leading-6">{{ __('welcome.threads_body') }}</p>
            </article>

            <article class="welcome-card rounded-3xl p-5" data-color="c">
                <h2 class="text-lg font-semibold">{{ __('lists.lists') }}</h2>
                <p class="mt-3 text-sm leading-6">{{ __('welcome.lists_body') }}</p>
            </article>

            <article class="welcome-card rounded-3xl p-5" data-color="e">
                <h2 class="text-lg font-semibold">{{ __('bookmarks.bookmarks') }}</h2>
                <p class="mt-3 text-sm leading-6">{{ __('welcome.bookmarks_body') }}</p>
            </article>

            <article class="welcome-card rounded-3xl p-5" data-color="f">
                <h2 class="text-lg font-semibold">{{ __('meals.meals') }}</h2>
                <p class="mt-3 text-sm leading-6">{{ __('welcome.meals_body') }}</p>
            </article>

            <article class="welcome-card rounded-3xl p-5 md:col-span-2 md:max-w-[calc(50%-0.5rem)] md:justify-self-center" data-color="g">
                <h2 class="text-lg font-semibold">{{ __('users.account') }}</h2>
                <p class="mt-3 text-sm leading-6">{{ __('welcome.account_body') }}</p>
            </article>
        </div>
    </div>
@endsection
