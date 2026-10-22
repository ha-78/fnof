@extends('layouts.front')

@section('content')
    <div class="mx-auto max-w-md rounded-lg border border-stone-200 bg-white p-8">
        <h1 class="text-center text-2xl font-semibold">{{ __('auth_pages.login') }}</h1>

        @if ($errors->any())
            <div class="mt-4 text-sm text-red-700">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="/login" class="mt-6 space-y-4">
            @csrf
            <input type="hidden" name="color_theme" id="login-color-theme" value="default">

            <label class="block text-sm font-medium" for="email">{{ __('auth_pages.email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="w-full rounded-md border border-stone-300 px-3 py-2">

            <label class="block text-sm font-medium" for="password">{{ __('auth_pages.password') }}</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" class="w-full rounded-md border border-stone-300 px-3 py-2">

            <button type="submit" class="w-full rounded-md bg-stone-900 px-3 py-2 text-white">{{ __('auth_pages.login') }}</button>
        </form>
    </div>

    <script>
        try {
            var theme = localStorage.getItem('fnof_theme');
            if (theme === 'default' || theme === 'light' || theme === 'dark' || theme === 'colorful') {
                document.getElementById('login-color-theme').value = theme;
            }
        } catch (e) {}
    </script>
@endsection
