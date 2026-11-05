<?php

use App\Enums\Role;
use App\Enums\Section;
use App\Models\Thread;
use App\Models\User;

it('uses the start section before a request', function () {
    expect(Section::current())->toBe(Section::Start);
});

it('paints the header from the current section', function (string $routeName, string $letter) {
    $this->actingAs(User::factory()->create())
        ->get(route($routeName))
        ->assertSuccessful()
        ->assertSee('--header-light: var(--color-'.$letter.'-3)', false)
        ->assertSee('--header-dark: var(--color-'.$letter.'-4)', false);
})->with([
    'start' => ['home', 'd'],
    'threads' => ['threads.index', 'a'],
    'lists' => ['lists.index', 'c'],
    'bookmarks' => ['bookmarks.index', 'e'],
    'account' => ['account', 'g'],
]);

it('paints a thread with the threads color', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('threads.show', Thread::factory()->create()))
        ->assertSuccessful()
        ->assertSee('--header-light: var(--color-a-3)', false)
        ->assertSee('--header-dark: var(--color-a-4)', false);
});

it('paints list editing and user management with their section colors', function () {
    $admin = User::factory()->withRole(Role::Admin)->create();

    $this->actingAs($admin)
        ->get(route('lists.edit'))
        ->assertSuccessful()
        ->assertSee('--header-light: var(--color-c-3)', false);

    $this->actingAs($admin)
        ->get(route('users.index'))
        ->assertSuccessful()
        ->assertSee('--header-light: var(--color-g-3)', false)
        ->assertSee('aria-current="page"', false)
        ->assertSee('background-color: var(--color-d-1)', false);
});

it('marks the current section icon and leaves the others in their own color', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('lists.index'))
        ->assertSuccessful()
        ->assertSee('background-color: var(--color-d-1)', false)
        ->assertSee('background-color: var(--color-a-2)', false)
        ->assertSee('background-color: var(--color-e-2)', false)
        ->assertSee('background-color: var(--color-g-2)', false)
        ->assertDontSee('background-color: var(--color-c-2)', false)
        ->assertSee('aria-current="page"', false);

    $this->actingAs(User::factory()->create())
        ->get(route('threads.index'))
        ->assertSuccessful()
        ->assertSee('href="'.route('threads.index').'"', false)
        ->assertSee('background-color: var(--color-d-1)', false)
        ->assertSee('background-color: var(--color-c-2)', false)
        ->assertSee('background-color: var(--color-e-2)', false)
        ->assertSee('background-color: var(--color-g-2)', false)
        ->assertDontSee('background-color: var(--color-a-2)', false)
        ->assertSee('M20.25 8.511c.884.284', false)
        ->assertSee('aria-current="page"', false);

    $this->actingAs(User::factory()->create())
        ->get(route('home'))
        ->assertSuccessful()
        ->assertSee('background-color: var(--color-a-2)', false)
        ->assertSee('background-color: var(--color-c-2)', false)
        ->assertSee('background-color: var(--color-e-2)', false)
        ->assertSee('background-color: var(--color-g-2)', false)
        ->assertDontSee('background-color: var(--color-d-1)', false)
        ->assertDontSee('aria-current="page"', false);
});

it('leaves the login header on the shared fallback', function () {
    $this->get(route('login'))
        ->assertSuccessful()
        ->assertDontSee('--header-light', false);
});
