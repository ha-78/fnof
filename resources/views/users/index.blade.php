@extends('layouts.app')

@section('title', __('users.users'))

@section('content')
    <div class="space-y-6">
        @if ($context)
            <x-ui.form-card :label="__('users.context')" :action="route('contexts.update', $context)" method="PATCH">
                <input type="hidden" name="form" value="context">

                <x-ui.field
                    :label="__('users.context_name')"
                    name="name"
                    id="context-name"
                    :value="old('form') === 'invite' ? $context->name : old('name', $context->name)"
                    :show-error="old('form') !== 'invite'"
                    required
                    maxlength="255"
                />

                <div class="flex justify-end">
                    <x-ui.button type="submit" variant="tonal">{{ __('users.save_context') }}</x-ui.button>
                </div>
            </x-ui.form-card>
        @endif

        @if ($canInvite)
            <x-ui.form-card :label="__('users.invites.heading')" :action="route('invites.store')" icon="plus">
                <input type="hidden" name="form" value="invite">
                <p class="px-0.5 text-[13px] text-muted">{{ __('users.invites.lifetime') }}</p>

                <x-ui.field
                    :label="__('users.name')"
                    name="name"
                    id="invite-name"
                    :value="old('form') === 'invite' ? old('name') : ''"
                    :show-error="old('form') === 'invite'"
                    required
                    maxlength="255"
                    autocomplete="off"
                />
                <x-ui.field
                    :label="__('users.email')"
                    name="email"
                    id="invite-email"
                    type="email"
                    :value="old('form') === 'invite' ? old('email') : ''"
                    required
                    maxlength="255"
                    autocomplete="off"
                />
                <x-ui.field
                    :label="__('users.role')"
                    name="role"
                    id="invite-role"
                    :options="collect($inviteRoles)->mapWithKeys(fn ($role) => [$role->value => $role->label()])->all()"
                    :selected="old('form') === 'invite' ? old('role') : $defaultInviteRole->value"
                    required
                />

                <x-ui.button type="submit" class="w-full">{{ __('users.invites.send') }}</x-ui.button>
            </x-ui.form-card>

            <x-ui.group :heading="__('users.invites.pending')">
                @forelse ($pendingInvites as $invite)
                    <div class="space-y-2 py-3 pr-1.5 pl-4">
                        <div class="flex items-start gap-2">
                            <div class="min-w-0 flex-1">
                                <p class="flex flex-wrap items-center gap-2">
                                    <span class="text-[15px] font-semibold">{{ $invite->name }}</span>
                                    <x-ui.badge>{{ $invite->role->label() }}</x-ui.badge>
                                </p>
                                <p class="truncate text-[13px] text-muted">{{ $invite->email }}</p>
                                <p class="text-[13px] text-muted">
                                    {{ __('users.invites.expires') }}
                                    <time datetime="{{ $invite->expires_at->toAtomString() }}">{{ $invite->expires_at->timezone(config('app.timezone'))->format('Y-m-d H:i') }}</time>
                                </p>
                            </div>

                            <form method="POST" action="{{ route('invites.destroy', $invite) }}">
                                @csrf
                                @method('DELETE')
                                <x-ui.button type="submit" variant="ghost" icon :aria-label="__('users.invites.remove')">
                                    <span class="text-muted"><x-icon name="trash" size="5" /></span>
                                </x-ui.button>
                            </form>
                        </div>

                        <div x-data="copyText" class="flex items-center gap-2 pr-2.5">
                            <input
                                x-ref="link"
                                type="text"
                                readonly
                                value="{{ $invite->link() }}"
                                aria-label="{{ __('users.invites.link') }}"
                                spellcheck="false"
                                autocomplete="off"
                                x-on:focus="$el.select()"
                                class="h-10 w-full min-w-0 rounded-field bg-field px-3 text-[13px] text-field-ink"
                            >
                            <x-ui.button variant="tonal" class="h-10 shrink-0" x-on:click="copy($refs.link.value)">
                                <x-icon name="link" size="4" stroke="2" />
                                <span x-show="! copied">{{ __('users.invites.copy') }}</span>
                                <span x-show="copied" x-cloak>{{ __('users.invites.copied') }}</span>
                            </x-ui.button>
                        </div>
                    </div>
                @empty
                    <p class="px-4 py-3 text-[15px] text-muted">{{ __('users.invites.empty') }}</p>
                @endforelse
            </x-ui.group>
        @endif

        <x-ui.group :heading="__('users.users')">
            @foreach ($users as $member)
                <a href="{{ route('users.show', $member) }}" class="flex min-h-13 items-center gap-3 px-4 py-2 text-ink">
                    <span aria-hidden="true" class="grid size-9 shrink-0 place-items-center rounded-full bg-tonal text-[14px] font-semibold text-tonal-ink">{{ Str::upper(Str::substr($member->name, 0, 1)) }}</span>
                    <span class="min-w-0 flex-1">
                        <span class="flex flex-wrap items-center gap-2">
                            <span class="text-[15px] font-semibold">{{ $member->name }}</span>
                            <x-ui.badge>{{ $member->role->label() }}</x-ui.badge>
                        </span>
                        <span class="block truncate text-[13px] text-muted">{{ $member->email }}</span>
                    </span>
                    <span class="sr-only">{{ __('users.manage') }}</span>
                    <span class="text-muted"><x-icon name="chevron-right" size="4" stroke="2" /></span>
                </a>
            @endforeach
        </x-ui.group>

        @if ($users->hasPages())
            <div>{{ $users->links() }}</div>
        @endif
    </div>
@endsection
