<?php

use App\Enums\Role;
use App\Models\Context;
use App\Models\Invite;
use App\Models\User;
use App\Models\UserRememberDevice;
use App\Services\DeviceRememberService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

it('shows invite links only to a god', function () {
    $god = User::factory()->withRole(Role::God)->create();
    $admin = User::factory()->withRole(Role::Admin)->create();

    $this->actingAs($god)
        ->get(route('users.index'))
        ->assertSuccessful()
        ->assertSee(__('users.invites.heading'))
        ->assertSee(__('users.invites.lifetime'))
        ->assertSee(__('users.invites.empty'));

    $this->actingAs($admin)
        ->get(route('users.index'))
        ->assertSuccessful()
        ->assertDontSee(__('users.invites.heading'))
        ->assertDontSee(__('users.invites.empty'));
});

it('creates a week-long invite link for the context', function (Role $role) {
    $god = User::factory()->withRole(Role::God)->create();
    $otherGod = User::factory()->withRole(Role::God)->create();
    $context = Context::query()->sole();

    $this->actingAs($god)
        ->post(route('invites.store'), [
            'form' => 'invite',
            'name' => '  Ada   Lovelace  ',
            'email' => '  Ada@Example.com ',
            'role' => $role->value,
        ])
        ->assertRedirect(route('users.index'))
        ->assertSessionHas('status', __('users.invites.created'));

    $invite = Invite::query()->sole();
    $lifetime = max(1, (int) config('invites.lifetime_days'));
    $stored = DB::table($invite->getTable())->where('id', $invite->id)->first();

    expect($invite->name)->toBe('Ada Lovelace')
        ->and($invite->email)->toBe('ada@example.com')
        ->and($invite->role)->toBe($role)
        ->and($invite->context_id)->toBe($context->id)
        ->and($invite->inviter_id)->toBe($god->id)
        ->and($invite->expires_at->between(
            now()->addDays($lifetime)->subMinute(),
            now()->addDays($lifetime)->addMinute(),
        ))->toBeTrue()
        ->and($stored->token_hash)->toBe(hash('sha256', $invite->token))
        ->and($stored->token)->not->toBe($invite->token)
        ->and(User::query()->where('email', 'ada@example.com')->exists())->toBeFalse();

    $this->actingAs($god)
        ->get(route('users.index'))
        ->assertSuccessful()
        ->assertSee($invite->link(), false)
        ->assertSee(__('users.invites.copy'))
        ->assertSee(__('users.invites.remove'))
        ->assertSee($invite->expires_at->timezone(config('app.timezone'))->format('Y-m-d H:i'))
        ->assertDontSee($invite->token_hash, false);

    $this->actingAs($otherGod)
        ->get(route('users.index'))
        ->assertSuccessful()
        ->assertSee($invite->link(), false);

    $this->actingAs(User::factory()->withRole(Role::Admin)->create())
        ->get(route('users.index'))
        ->assertDontSee($invite->link(), false)
        ->assertDontSee('ada@example.com');
})->with([
    'reader' => Role::Reader,
    'member' => Role::Member,
    'admin' => Role::Admin,
    'god' => Role::God,
]);

it('rejects an invite without a real name, email, and role', function () {
    $god = User::factory()->withRole(Role::God)->create();

    $this->actingAs($god)
        ->from(route('users.index'))
        ->post(route('invites.store'), [
            'form' => 'invite',
            'name' => '   ',
            'email' => 'not-an-email',
            'role' => 'owner',
        ])
        ->assertRedirect(route('users.index'))
        ->assertSessionHasErrors(['name', 'email', 'role']);

    expect(Invite::query()->exists())->toBeFalse();
});

it('rejects an email that already belongs to a person or a pending invite', function () {
    $god = User::factory()->withRole(Role::God)->create();
    User::factory()->create(['email' => 'finns@example.com']);

    $this->actingAs($god)
        ->from(route('users.index'))
        ->post(route('invites.store'), [
            'name' => 'Finns',
            'email' => 'finns@example.com',
            'role' => Role::Reader->value,
        ])
        ->assertRedirect(route('users.index'))
        ->assertSessionHasErrors('email');

    $this->actingAs($god)
        ->post(route('invites.store'), [
            'name' => 'Ny',
            'email' => 'ny@example.com',
            'role' => Role::Reader->value,
        ])
        ->assertRedirect(route('users.index'));

    $this->actingAs($god)
        ->from(route('users.index'))
        ->post(route('invites.store'), [
            'name' => 'Ny igen',
            'email' => 'NY@example.com',
            'role' => Role::Member->value,
        ])
        ->assertRedirect(route('users.index'))
        ->assertSessionHasErrors('email');

    expect(Invite::query()->count())->toBe(1);
});

it('lets a god invite the same email again after the previous link expired', function () {
    $god = User::factory()->withRole(Role::God)->create();

    Invite::factory()->expired()->create([
        'context_id' => Context::query()->sole()->id,
        'inviter_id' => $god->id,
        'email' => 'ny@example.com',
    ]);

    $this->actingAs($god)
        ->post(route('invites.store'), [
            'name' => 'Ny person',
            'email' => 'ny@example.com',
            'role' => Role::Reader->value,
        ])
        ->assertRedirect(route('users.index'));

    $invite = Invite::query()->where('email', 'ny@example.com')->sole();

    expect($invite->expires_at->isFuture())->toBeTrue();
});

it('stops anyone except a god from creating or opening the invite form', function () {
    $admin = User::factory()->withRole(Role::Admin)->create();
    $reader = User::factory()->withRole(Role::Reader)->create();
    $payload = [
        'name' => 'Ny',
        'email' => 'ny@example.com',
        'role' => Role::Member->value,
    ];

    $this->post(route('invites.store'), $payload)->assertRedirect(route('login'));
    $this->actingAs($admin)->post(route('invites.store'), $payload)->assertForbidden();
    $this->actingAs($reader)->post(route('invites.store'), $payload)->assertForbidden();

    expect(Invite::query()->exists())->toBeFalse();
});

it('lets a god remove a pending invite from the context', function () {
    $god = User::factory()->withRole(Role::God)->create();
    $otherGod = User::factory()->withRole(Role::God)->create();
    $admin = User::factory()->withRole(Role::Admin)->create();

    $this->actingAs($god)->post(route('invites.store'), [
        'name' => 'Ny person',
        'email' => 'ny@example.com',
        'role' => Role::Member->value,
    ])->assertRedirect(route('users.index'));

    $invite = Invite::query()->sole();
    $foreign = Invite::factory()->create([
        'context_id' => Context::query()->create(['name' => 'Annan'])->id,
        'inviter_id' => $god->id,
    ]);

    auth()->logout();
    $this->flushSession();

    $this->delete(route('invites.destroy', $invite))->assertRedirect(route('login'));

    $this->actingAs($admin)
        ->delete(route('invites.destroy', $invite))
        ->assertForbidden();

    $this->actingAs($god)
        ->delete(route('invites.destroy', $foreign))
        ->assertForbidden();

    $this->assertModelExists($invite);
    $this->assertModelExists($foreign);

    $this->actingAs($otherGod)
        ->delete(route('invites.destroy', $invite))
        ->assertRedirect(route('users.index'))
        ->assertSessionHas('status', __('users.invites.removed'));

    $this->assertModelMissing($invite);
    $this->assertModelExists($foreign);
});

it('lets the invited person choose a password and stay signed in on the device', function () {
    $god = User::factory()->withRole(Role::God)->create();
    $context = Context::query()->sole();

    $this->actingAs($god)->post(route('invites.store'), [
        'name' => 'Ny person',
        'email' => 'ny@example.com',
        'role' => Role::Admin->value,
    ])->assertRedirect(route('users.index'));

    $invite = Invite::query()->sole();

    auth()->logout();
    $this->flushSession();

    $this->get($invite->link())
        ->assertSuccessful()
        ->assertSee(__('users.invites.accept_heading'))
        ->assertSee('Ny person')
        ->assertSee('ny@example.com')
        ->assertSee($context->name);

    $response = $this->post(route('invites.accept.store', ['token' => $invite->token]), [
        'password' => 'password',
        'password_confirmation' => 'password',
        'color_theme' => 'dark',
    ]);

    $user = User::query()->where('email', 'ny@example.com')->firstOrFail();
    $cookieName = app(DeviceRememberService::class)->cookieName();

    $response->assertRedirect(route('home'));
    $response->assertCookie($cookieName);
    $this->assertAuthenticatedAs($user);
    $this->assertModelMissing($invite);

    $plain = $response->getCookie($cookieName)->getValue();
    $device = UserRememberDevice::query()->whereBelongsTo($user)->first();

    expect($user->role)->toBe(Role::Admin)
        ->and($user->memberships()->sole()->context_id)->toBe($context->id)
        ->and(Hash::check('password', $user->password))->toBeTrue()
        ->and($device)->not->toBeNull()
        ->and($device->color_theme)->toBe('dark')
        ->and($device->token)->toBe(hash('sha256', Str::after($plain, '|')));

    $this->get(route('home'))->assertSuccessful();

    auth()->logout();
    $this->flushSession();

    $this->withCookie($cookieName, $plain)
        ->get(route('home'))
        ->assertSuccessful();

    expect(auth()->id())->toBe($user->id);

    $this->get($invite->link())
        ->assertNotFound()
        ->assertSee(__('users.invites.invalid'));

    $this->actingAs($god)
        ->get(route('users.index'))
        ->assertSee('ny@example.com')
        ->assertSee(__('users.invites.empty'))
        ->assertDontSee($invite->link(), false);
});

it('keeps the invite when the password does not match', function () {
    $god = User::factory()->withRole(Role::God)->create();

    $this->actingAs($god)->post(route('invites.store'), [
        'name' => 'Ny person',
        'email' => 'ny@example.com',
        'role' => Role::Reader->value,
    ]);

    $invite = Invite::query()->sole();

    auth()->logout();
    $this->flushSession();

    $this->from($invite->link())
        ->post(route('invites.accept.store', ['token' => $invite->token]), [
            'password' => 'password',
            'password_confirmation' => 'annat-losenord',
        ])
        ->assertRedirect($invite->link())
        ->assertSessionHasErrors('password');

    expect(User::query()->where('email', 'ny@example.com')->exists())->toBeFalse();
    $this->assertModelExists($invite);
});

it('does not accept an invite after that email has an account', function () {
    $god = User::factory()->withRole(Role::God)->create();

    $this->actingAs($god)->post(route('invites.store'), [
        'name' => 'Ny person',
        'email' => 'ny@example.com',
        'role' => Role::Reader->value,
    ]);

    $invite = Invite::query()->sole();
    $existing = User::factory()->create(['email' => 'ny@example.com']);

    auth()->logout();
    $this->flushSession();

    $this->from($invite->link())
        ->post(route('invites.accept.store', ['token' => $invite->token]), [
            'password' => 'password',
            'password_confirmation' => 'password',
        ])
        ->assertRedirect($invite->link())
        ->assertSessionHasErrors('email');

    expect($existing->refresh()->memberships)->toHaveCount(1)
        ->and($existing->role)->toBe(Role::Member);
    $this->assertModelExists($invite);
});

it('rejects an invite link after a week', function () {
    $god = User::factory()->withRole(Role::God)->create();

    $this->actingAs($god)->post(route('invites.store'), [
        'name' => 'Ny person',
        'email' => 'ny@example.com',
        'role' => Role::Reader->value,
    ]);

    auth()->logout();
    $this->flushSession();

    $invite = Invite::query()->sole();

    $this->travelTo($invite->expires_at->copy()->subHour());

    $this->get($invite->link())->assertSuccessful();

    $this->travelTo($invite->expires_at->copy()->addSecond());

    $this->get($invite->link())
        ->assertNotFound()
        ->assertSee(__('users.invites.invalid'));
});

it('rejects an unknown invite link', function () {
    $this->get(route('invites.accept', ['token' => str_repeat('a', 64)]))
        ->assertNotFound()
        ->assertSee(__('users.invites.invalid'));

    $this->get('/invites/'.str_repeat('a', 10))->assertNotFound();
});

it('drops expired invites from the pending list', function () {
    $god = User::factory()->withRole(Role::God)->create();

    $invite = Invite::factory()->expired()->create([
        'context_id' => Context::query()->sole()->id,
        'inviter_id' => $god->id,
        'name' => 'Gammal person',
        'email' => 'gammal@example.com',
    ]);

    $this->actingAs($god)
        ->get(route('users.index'))
        ->assertSuccessful()
        ->assertDontSee('gammal@example.com')
        ->assertSee(__('users.invites.empty'));

    $this->assertModelMissing($invite);
});
