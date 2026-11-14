@extends('layouts.front')

@section('content')
    <div class="mx-auto max-w-sm space-y-4 rounded-card border border-surface-line bg-surface p-5 shadow-sm">
        <h1 class="text-xl font-semibold tracking-tight">{{ __('auth_pages.login') }}</h1>

        <form method="POST" action="/login" class="space-y-3">
            @csrf
            <input type="hidden" name="color_theme" id="login-color-theme" value="default">

            <x-ui.field :label="__('auth_pages.email')" name="email" type="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-ui.field :label="__('auth_pages.password')" name="password" type="password" required autocomplete="current-password" />

            <x-ui.button type="submit" class="mt-2 w-full">{{ __('auth_pages.login') }}</x-ui.button>
        </form>
    </div>

    <script>
        try {
            var theme = localStorage.getItem('fnof_theme');
            if (theme === 'colorful') {
                theme = 'light';
            }
            if (theme === 'default' || theme === 'light' || theme === 'dark') {
                document.getElementById('login-color-theme').value = theme;
            }
        } catch (e) {}
    </script>
@endsection
