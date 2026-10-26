@extends('layouts.front')

@section('content')
    <div class="mx-auto max-w-md rounded-3xl bg-rose p-8 text-ink">
        <h1 class="text-center text-2xl font-semibold tracking-tight">{{ __('auth_pages.login') }}</h1>

        @if ($errors->any())
            <div class="mt-4 rounded-2xl bg-danger-soft px-3 py-2 text-sm text-danger">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="/login" class="mt-6 space-y-4">
            @csrf
            <input type="hidden" name="color_theme" id="login-color-theme" value="default">

            <label class="block text-sm font-medium" for="email">{{ __('auth_pages.email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="w-full rounded-2xl bg-field px-3 py-2 text-ink">

            <label class="block text-sm font-medium" for="password">{{ __('auth_pages.password') }}</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" class="w-full rounded-2xl bg-field px-3 py-2 text-ink">

            <button type="submit" class="w-full rounded-full bg-button px-3 py-2 font-medium text-button-ink hover:opacity-90">{{ __('auth_pages.login') }}</button>
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
