<?php

use App\Enums\Feature;
use App\Enums\Role;
use App\Models\Context;
use App\Models\ContextUser;
use App\Models\User;
use App\Models\UserFeature;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

it('does not offer public registration', function () {
    $this->get('/')
        ->assertSuccessful()
        ->assertSee('href="'.route('login').'"', false)
        ->assertSee('welcome-card', false)
        ->assertSee(__('welcome.intro'))
        ->assertSee('M6.75 3v2.25M17.25 3v2.25', false)
        ->assertDontSee('var(--color-', false)
        ->assertDontSee('Documentation', false)
        ->assertDontSee('/register', false);

    $this->get('/register')->assertNotFound();

    $this->post('/register', [
        'name' => 'Ny läsare',
        'email' => 'reader@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => Role::God->value,
    ])->assertNotFound();

    expect(User::query()->where('email', 'reader@example.com')->exists())->toBeFalse();
});

it('creates a god user from the terminal', function () {
    $this->artisan('users:create-god', [
        '--name' => 'Första God',
        '--email' => 'god@example.com',
        '--password' => 'password',
    ])->assertSuccessful();

    $user = User::query()->where('email', 'god@example.com')->first();
    $context = Context::query()->sole();
    $membership = $context->memberships()->sole();

    expect($user)->not->toBeNull()
        ->and($context->name)->toBe('Första God')
        ->and($membership->user_id)->toBe($user->id)
        ->and($membership->role)->toBe(Role::God)
        ->and(Hash::check('password', $user->password))->toBeTrue();
});

it('adds another god to the existing context', function () {
    $this->artisan('users:create-god', [
        '--name' => 'Första God',
        '--email' => 'god@example.com',
        '--password' => 'password',
    ])->assertSuccessful();

    $this->artisan('users:create-god', [
        '--name' => 'Andra God',
        '--email' => 'other@example.com',
        '--password' => 'password',
    ])->assertSuccessful();

    $memberships = Context::query()->sole()->memberships;

    expect(Context::query()->count())->toBe(1)
        ->and($memberships)->toHaveCount(2)
        ->and($memberships->every(fn (ContextUser $membership): bool => $membership->role === Role::God))->toBeTrue();
});

it('stores a pasted god password without terminal paste markers', function () {
    $this->artisan('users:create-god', [
        '--name' => 'Första God',
        '--email' => 'god@example.com',
        '--password' => "\e[200~password\e[201~",
    ])->assertSuccessful();

    $user = User::query()->where('email', 'god@example.com')->first();

    expect(Hash::check('password', $user->password))->toBeTrue();
});

it('rejects a duplicate god email', function () {
    User::factory()->create(['email' => 'god@example.com']);

    $this->artisan('users:create-god', [
        '--name' => 'Annan',
        '--email' => 'god@example.com',
        '--password' => 'password',
    ])->assertFailed();
});

it('lets a god assign any role', function (Role $target) {
    $god = User::factory()->withRole(Role::God)->create();
    $reader = User::factory()->create();

    $this->actingAs($god)
        ->patch(route('users.role.update', $reader), ['role' => $target->value])
        ->assertRedirect(route('users.show', $reader));

    expect($reader->refresh()->role)->toBe($target);
})->with([
    'reader' => Role::Reader,
    'member' => Role::Member,
    'admin' => Role::Admin,
    'god' => Role::God,
]);

it('lets an admin turn a reader into a member and clears permission overrides', function () {
    $admin = User::factory()->withRole(Role::Admin)->create();
    $reader = User::factory()->withRole(Role::Reader)->create();

    $membership = $reader->contextMembership();

    DB::table((new UserFeature)->getTable())->insert([
        'context_user_id' => $membership->getKey(),
        'feature' => 'legacy',
        'granted' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($admin)
        ->patch(route('users.role.update', $reader), ['role' => Role::Member->value])
        ->assertRedirect(route('users.show', $reader));

    expect($reader->refresh()->role)->toBe(Role::Member)
        ->and($membership->featureOverrides()->exists())->toBeFalse();
});

it('rejects role changes an admin is not allowed to make', function (Role $subjectRole, Role $target) {
    $admin = User::factory()->withRole(Role::Admin)->create();
    $subject = User::factory()->withRole($subjectRole)->create();

    $this->actingAs($admin)
        ->patch(route('users.role.update', $subject), ['role' => $target->value])
        ->assertForbidden();

    expect($subject->refresh()->role)->toBe($subjectRole);
})->with([
    'reader to god' => [Role::Reader, Role::God],
    'reader to admin' => [Role::Reader, Role::Admin],
    'member to reader' => [Role::Member, Role::Reader],
    'admin to member' => [Role::Admin, Role::Member],
    'god to reader' => [Role::God, Role::Reader],
]);

it('keeps the last god user', function () {
    $god = User::factory()->withRole(Role::God)->create();

    $this->actingAs($god)
        ->from(route('users.show', $god))
        ->patch(route('users.role.update', $god), ['role' => Role::Admin->value])
        ->assertRedirect(route('users.show', $god))
        ->assertSessionHasErrors('role');

    expect($god->refresh()->role)->toBe(Role::God);
});

it('lets a god change another god when one remains', function () {
    $god = User::factory()->withRole(Role::God)->create();
    $other = User::factory()->withRole(Role::God)->create();

    $this->actingAs($god)
        ->patch(route('users.role.update', $other), ['role' => Role::Admin->value])
        ->assertRedirect(route('users.show', $other));

    expect($other->refresh()->role)->toBe(Role::Admin);
});

it('shows each person their account, and the user list only to an admin', function () {
    $admin = User::factory()->withRole(Role::Admin)->create();
    $reader = User::factory()->create();

    $this->actingAs($admin)
        ->get(route('account'))
        ->assertSuccessful()
        ->assertSee($admin->email)
        ->assertSee(__('users.log_out'))
        ->assertSee(route('users.index'), false)
        ->assertSee(__('users.household'))
        ->assertSee('x-data="themeToggle"', false)
        ->assertDontSee('var(--color-', false)
        ->assertDontSee(__('users.role').':', false)
        ->assertSeeInOrder([
            __('themes.label'),
            __('themes.light'),
            __('themes.dark'),
            __('themes.default'),
        ]);

    $this->actingAs($admin)
        ->get(route('users.index'))
        ->assertSuccessful()
        ->assertSee($reader->email)
        ->assertSee(__('users.context'))
        ->assertSee(__('users.manage'))
        ->assertDontSee(__('users.log_out'))
        ->assertSee(__('users.invites.heading'))
        ->assertSee('href="'.route('users.show', $reader).'"', false)
        ->assertDontSee('var(--color-', false);

    $this->actingAs($reader)
        ->get(route('account'))
        ->assertSuccessful()
        ->assertSee($reader->name)
        ->assertSee($reader->email)
        ->assertSee(__('users.log_out'))
        ->assertDontSee(route('users.index'), false);

    $this->actingAs($reader)
        ->get(route('users.index'))
        ->assertForbidden();
});

it('sends guests to login', function () {
    $context = Context::query()->create(['name' => 'Familjen']);

    $this->get(route('account'))->assertRedirect(route('login'));
    $this->get(route('users.index'))->assertRedirect(route('login'));
    $this->patch(route('contexts.update', $context), ['name' => 'Namn'])->assertRedirect(route('login'));
});

it('lets an admin rename the context from the users page', function () {
    $admin = User::factory()->withRole(Role::Admin)->create();
    $context = Context::query()->sole();

    $this->actingAs($admin)
        ->get(route('users.index'))
        ->assertSuccessful()
        ->assertSee($context->name);

    $this->actingAs($admin)
        ->patch(route('contexts.update', $context), ['name' => '  Familjen  '])
        ->assertRedirect(route('users.index'))
        ->assertSessionHas('status', __('users.context_updated'));

    expect($context->refresh()->name)->toBe('Familjen');
});

it('rejects an empty context name', function () {
    $admin = User::factory()->withRole(Role::Admin)->create();
    $context = Context::query()->sole();

    $this->actingAs($admin)
        ->from(route('users.index'))
        ->patch(route('contexts.update', $context), ['name' => '   '])
        ->assertRedirect(route('users.index'))
        ->assertSessionHasErrors('name');

    expect($context->refresh()->name)->toBe($admin->name);
});

it('stops a reader from renaming the context', function () {
    User::factory()->withRole(Role::Admin)->create();
    $reader = User::factory()->withRole(Role::Reader)->create();
    $context = Context::query()->sole();
    $name = $context->name;

    $this->actingAs($reader)
        ->patch(route('contexts.update', $context), ['name' => 'Annat namn'])
        ->assertForbidden();

    expect($context->refresh()->name)->toBe($name);
});

it('lets an admin save permissions for a member', function () {
    $admin = User::factory()->withRole(Role::Admin)->create();
    $member = User::factory()->create();

    $features = collect(Feature::cases())
        ->mapWithKeys(fn (Feature $feature): array => [$feature->value => '0'])
        ->all();
    $features[Feature::CreateThreads->value] = '1';

    $this->actingAs($admin)
        ->put(route('users.features.update', $member), ['features' => $features])
        ->assertRedirect(route('users.show', $member))
        ->assertSessionHas('status', __('users.features_updated'));

    $override = $member->contextMembership()->featureOverrides()->sole();

    expect($override->feature)->toBe(Feature::CreateThreads)
        ->and($override->granted)->toBeTrue();
});

it('groups permissions by section on the user page', function () {
    $admin = User::factory()->withRole(Role::Admin)->create();
    $member = User::factory()->create();

    $this->actingAs($admin)
        ->get(route('users.show', $member))
        ->assertSuccessful()
        ->assertSee(__('users.features.create-threads'), false)
        ->assertSee(__('threads.threads'), false)
        ->assertSee(__('welcome.calendar_title'), false)
        ->assertSee(__('users.features.create-notifications'), false)
        ->assertDontSee(__('users.no_permissions'), false);
});

it('shows each permission group in its section color with switches', function () {
    $admin = User::factory()->withRole(Role::Admin)->create();
    $member = User::factory()->create();

    $this->actingAs($admin)
        ->get(route('users.show', $member))
        ->assertSuccessful()
        ->assertSee('data-color="a"', false)
        ->assertSee('name="features[create-threads]" value="1"', false)
        ->assertSee('peer sr-only', false)
        ->assertSee('href="'.route('users.index').'"', false)
        ->assertDontSee('var(--color-', false)
        ->assertDontSee('style="background-color', false);
});

it('hides permission toggles for a reader', function () {
    $admin = User::factory()->withRole(Role::Admin)->create();
    $reader = User::factory()->withRole(Role::Reader)->create();

    $this->actingAs($admin)
        ->get(route('users.show', $reader))
        ->assertSuccessful()
        ->assertDontSee(__('users.permissions'), false)
        ->assertDontSee(__('users.features.create-threads'), false);

    $this->actingAs($admin)
        ->put(route('users.features.update', $reader), ['features' => []])
        ->assertForbidden();
});

it('stops an admin from assigning permissions to a god', function () {
    $admin = User::factory()->withRole(Role::Admin)->create();
    $god = User::factory()->withRole(Role::God)->create();

    $this->actingAs($admin)
        ->put(route('users.features.update', $god), ['features' => []])
        ->assertForbidden();
});

it('rejects an unknown permission', function () {
    $admin = User::factory()->withRole(Role::Admin)->create();
    $reader = User::factory()->create();

    $this->actingAs($admin)
        ->from(route('users.show', $reader))
        ->put(route('users.features.update', $reader), [
            'features' => ['nope' => '1'],
        ])
        ->assertRedirect(route('users.show', $reader))
        ->assertSessionHasErrors('features');
});
