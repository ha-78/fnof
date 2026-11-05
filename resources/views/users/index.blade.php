@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-semibold tracking-tight">{{ __('users.users') }}</h1>

    @if ($context)
        <section class="mt-8">
            <h2 class="text-lg font-medium">{{ __('users.context') }}</h2>

            <form method="POST" action="{{ route('contexts.update', $context) }}" class="account-form mt-4 space-y-3 rounded-3xl p-5">
                @csrf
                @method('PATCH')
                <input type="hidden" name="form" value="context">

                <label class="block text-sm font-medium" for="name">{{ __('users.context_name') }}</label>
                <input id="name" name="name" type="text" value="{{ old('form') === 'invite' ? $context->name : old('name', $context->name) }}" required maxlength="255" class="w-full rounded-2xl bg-field px-3 py-2 text-sm text-ink">

                @error('name')
                    @if (old('form') !== 'invite')
                        <p class="rounded-2xl bg-danger-soft px-3 py-2 text-sm text-danger">{{ $message }}</p>
                    @endif
                @enderror

                <div class="flex justify-end">
                    <button type="submit" class="rounded-full bg-[var(--color-g-5)] px-4 py-2 text-sm font-medium text-[var(--color-g-1)] hover:opacity-90">{{ __('users.save_context') }}</button>
                </div>
            </form>
        </section>
    @endif

    @if ($canInvite)
        <section class="mt-8">
            <h2 class="text-lg font-medium">{{ __('users.invites.heading') }}</h2>
            <p class="mt-2 text-sm text-muted">{{ __('users.invites.lifetime') }}</p>

            <form method="POST" action="{{ route('invites.store') }}" class="account-form mt-4 space-y-3 rounded-3xl p-5">
                @csrf
                <input type="hidden" name="form" value="invite">

                <label class="block text-sm font-medium" for="invite-name">{{ __('users.name') }}</label>
                <input id="invite-name" name="name" type="text" value="{{ old('form') === 'invite' ? old('name') : '' }}" required maxlength="255" autocomplete="off" class="w-full rounded-2xl bg-field px-3 py-2 text-sm text-ink">

                @error('name')
                    @if (old('form') === 'invite')
                        <p class="rounded-2xl bg-danger-soft px-3 py-2 text-sm text-danger">{{ $message }}</p>
                    @endif
                @enderror

                <label class="block text-sm font-medium" for="invite-email">{{ __('users.email') }}</label>
                <input id="invite-email" name="email" type="email" value="{{ old('form') === 'invite' ? old('email') : '' }}" required maxlength="255" autocomplete="off" class="w-full rounded-2xl bg-field px-3 py-2 text-sm text-ink">

                @error('email')
                    <p class="rounded-2xl bg-danger-soft px-3 py-2 text-sm text-danger">{{ $message }}</p>
                @enderror

                <label class="block text-sm font-medium" for="invite-role">{{ __('users.role') }}</label>
                <select id="invite-role" name="role" required class="rounded-2xl bg-field px-3 py-2 text-sm text-ink">
                    @foreach ($inviteRoles as $role)
                        <option value="{{ $role->value }}" @selected((old('form') === 'invite' ? old('role') : $defaultInviteRole->value) === $role->value)>{{ $role->label() }}</option>
                    @endforeach
                </select>

                @error('role')
                    <p class="rounded-2xl bg-danger-soft px-3 py-2 text-sm text-danger">{{ $message }}</p>
                @enderror

                <div class="flex justify-end">
                    <button type="submit" class="rounded-full bg-[var(--color-g-5)] px-4 py-2 text-sm font-medium text-[var(--color-g-1)] hover:opacity-90">{{ __('users.invites.send') }}</button>
                </div>
            </form>
        </section>

        <section class="mt-8">
            <h2 class="text-lg font-medium">{{ __('users.invites.pending') }}</h2>

            @if ($pendingInvites->isEmpty())
                <p class="account-surface mt-4 rounded-3xl px-4 py-3 text-sm text-ink">{{ __('users.invites.empty') }}</p>
            @else
                <div class="account-surface mt-4 overflow-x-auto rounded-3xl">
                    <table class="account-table w-full text-left text-sm">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 font-medium text-ink">{{ __('users.name') }}</th>
                                <th class="px-4 py-3 font-medium text-ink">{{ __('users.email') }}</th>
                                <th class="px-4 py-3 font-medium text-ink">{{ __('users.role') }}</th>
                                <th class="px-4 py-3 font-medium text-ink">{{ __('users.invites.expires') }}</th>
                                <th class="px-4 py-3 font-medium text-ink">{{ __('users.invites.link') }}</th>
                                <th class="px-4 py-3 font-medium text-ink"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pendingInvites as $invite)
                                <tr>
                                    <td class="px-4 py-3">{{ $invite->name }}</td>
                                    <td class="px-4 py-3">{{ $invite->email }}</td>
                                    <td class="px-4 py-3">{{ $invite->role->label() }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 tabular-nums">
                                        <time datetime="{{ $invite->expires_at->toAtomString() }}">{{ $invite->expires_at->timezone(config('app.timezone'))->format('Y-m-d H:i') }}</time>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div x-data="copyText" class="flex min-w-72 items-center gap-2">
                                            <input
                                                x-ref="link"
                                                type="text"
                                                readonly
                                                value="{{ $invite->link() }}"
                                                aria-label="{{ __('users.invites.link') }}"
                                                spellcheck="false"
                                                autocomplete="off"
                                                x-on:focus="$el.select()"
                                                class="w-full min-w-0 rounded-2xl bg-field px-3 py-2 text-sm text-ink"
                                            >
                                            <button type="button" class="shrink-0 rounded-full bg-chip px-3 py-1 text-ink hover:bg-pill-active" x-on:click="copy($refs.link.value)">
                                                <span x-show="!copied">{{ __('users.invites.copy') }}</span>
                                                <span x-show="copied" x-cloak>{{ __('users.invites.copied') }}</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <form method="POST" action="{{ route('invites.destroy', $invite) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-full bg-danger-soft px-3 py-1 text-danger hover:opacity-90">{{ __('users.invites.remove') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    @endif

    <section class="mt-8">
        <h2 class="text-lg font-medium">{{ __('users.users') }}</h2>

        <div class="account-surface mt-4 overflow-x-auto rounded-3xl">
            <table class="account-table w-full text-left text-sm">
                <thead>
                    <tr>
                        <th class="px-4 py-3 font-medium text-ink">{{ __('users.name') }}</th>
                        <th class="px-4 py-3 font-medium text-ink">{{ __('users.email') }}</th>
                        <th class="px-4 py-3 font-medium text-ink">{{ __('users.role') }}</th>
                        <th class="px-4 py-3 font-medium text-ink"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $member)
                        <tr>
                            <td class="px-4 py-3">{{ $member->name }}</td>
                            <td class="px-4 py-3">{{ $member->email }}</td>
                            <td class="px-4 py-3">{{ $member->role->label() }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('users.show', $member) }}" class="inline-flex rounded-full bg-chip px-3 py-1 text-ink hover:bg-pill-active">{{ __('users.manage') }}</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $users->links() }}</div>
    </section>
@endsection
