<?php

use App\Actions\Fortify\CreateNewUser;
use App\Enums\Role;
use App\Models\User;
use App\Models\UserFeature;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

it('registers a reader and ignores a submitted role', function () {
    $user = app(CreateNewUser::class)->create([
        'name' => 'Ny läsare',
        'email' => 'reader@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => Role::God->value,
    ]);

    expect($user->role)->toBe(Role::Reader);
});

it('creates a god user from the terminal', function () {
    $this->artisan('users:create-god', [
        '--name' => 'Första God',
        '--email' => 'god@example.com',
        '--password' => 'password',
    ])->assertSuccessful();

    $user = User::query()->where('email', 'god@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->role)->toBe(Role::God)
        ->and(Hash::check('password', $user->password))->toBeTrue();
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
    'user' => Role::User,
    'admin' => Role::Admin,
    'god' => Role::God,
]);

it('lets an admin turn a reader into a user and clears permission overrides', function () {
    $admin = User::factory()->withRole(Role::Admin)->create();
    $reader = User::factory()->create();

    DB::table((new UserFeature)->getTable())->insert([
        'user_id' => $reader->getKey(),
        'feature' => 'legacy',
        'granted' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($admin)
        ->patch(route('users.role.update', $reader), ['role' => Role::User->value])
        ->assertRedirect(route('users.show', $reader));

    expect($reader->refresh()->role)->toBe(Role::User)
        ->and(UserFeature::query()->whereBelongsTo($reader)->exists())->toBeFalse();
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
    'user to reader' => [Role::User, Role::Reader],
    'admin to user' => [Role::Admin, Role::User],
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

it('shows the user list to an admin and hides it from a reader', function () {
    $admin = User::factory()->withRole(Role::Admin)->create();
    $reader = User::factory()->create();

    $this->actingAs($admin)
        ->get(route('users.index'))
        ->assertSuccessful()
        ->assertSee($reader->email);

    $this->actingAs($reader)
        ->get(route('users.index'))
        ->assertForbidden();
});

it('sends guests to login', function () {
    $this->get(route('users.index'))->assertRedirect(route('login'));
});

it('lets an admin save permissions for a reader', function () {
    $admin = User::factory()->withRole(Role::Admin)->create();
    $reader = User::factory()->create();

    $this->actingAs($admin)
        ->put(route('users.features.update', $reader), ['features' => []])
        ->assertRedirect(route('users.show', $reader));
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
