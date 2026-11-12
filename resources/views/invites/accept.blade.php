@extends('layouts.front')

@section('content')
    <div class="mx-auto max-w-md rounded-3xl bg-panel p-8 text-ink">
        <h1 class="text-center text-2xl font-semibold tracking-tight">{{ __('users.invites.accept_heading') }}</h1>
        <p class="mt-3 text-center text-lg font-medium">{{ $invite->name }}</p>
        <p class="mt-2 text-center text-sm text-muted">{{ __('users.invites.accept_intro', ['context' => $invite->context->name]) }}</p>

        @if ($errors->any())
            <div class="mt-4 rounded-2xl bg-danger-soft px-3 py-2 text-sm text-danger">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('invites.accept.store', ['token' => $invite->token]) }}" class="mt-6 space-y-4">
            @csrf
            <input type="hidden" name="color_theme" id="invite-color-theme" value="default">

            <label class="block text-sm font-medium" for="email">{{ __('users.email') }}</label>
            <input id="email" type="email" name="email" value="{{ $invite->email }}" readonly autocomplete="username" class="w-full rounded-2xl bg-field px-3 py-2 text-ink">

            <label class="block text-sm font-medium" for="password">{{ __('users.password') }}</label>
            <input id="password" type="password" name="password" required autocomplete="new-password" class="w-full rounded-2xl bg-field px-3 py-2 text-ink">

            <label class="block text-sm font-medium" for="password_confirmation">{{ __('users.password_confirmation') }}</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="w-full rounded-2xl bg-field px-3 py-2 text-ink">

            <button type="submit" class="w-full rounded-full bg-button px-3 py-2 font-medium text-button-ink hover:opacity-90">{{ __('users.invites.save_password') }}</button>
        </form>
    </div>

    <script>
        try {
            var theme = localStorage.getItem('fnof_theme');
            if (theme === 'colorful') {
                theme = 'light';
            }
            if (theme === 'default' || theme === 'light' || theme === 'dark') {
                document.getElementById('invite-color-theme').value = theme;
            }
        } catch (e) {}
    </script>
@endsection
