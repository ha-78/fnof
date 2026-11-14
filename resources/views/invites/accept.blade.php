@extends('layouts.front')

@section('content')
    <div class="mx-auto max-w-sm space-y-4 rounded-card border border-surface-line bg-surface p-5 shadow-sm">
        <div>
            <h1 class="text-xl font-semibold tracking-tight">{{ __('users.invites.accept_heading') }}</h1>
            <p class="mt-2 text-[15px] font-semibold">{{ $invite->name }}</p>
            <p class="text-[13px] text-muted">{{ __('users.invites.accept_intro', ['context' => $invite->context->name]) }}</p>
        </div>

        <form method="POST" action="{{ route('invites.accept.store', ['token' => $invite->token]) }}" class="space-y-3">
            @csrf
            <input type="hidden" name="color_theme" id="invite-color-theme" value="default">

            <x-ui.field :label="__('users.email')" name="email" type="email" :value="$invite->email" readonly autocomplete="username" />
            <x-ui.field :label="__('users.password')" name="password" type="password" required autocomplete="new-password" />
            <x-ui.field :label="__('users.password_confirmation')" name="password_confirmation" type="password" required autocomplete="new-password" />

            <x-ui.button type="submit" class="mt-2 w-full">{{ __('users.invites.save_password') }}</x-ui.button>
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
