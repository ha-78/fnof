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
        ->assertSee('data-section="'.$letter.'"', false)
        ->assertSee('data-color="'.$letter.'"', false)
        ->assertDontSee('--header-light', false)
        ->assertDontSee('style="--', false)
        ->assertSee('href="'.route('home').'"', false)
        ->assertSee('m2.25 12 8.954-8.955', false)
        ->assertDontSee(__('layout.brand'), false)
        ->assertDontSee('M15.75 19.5 8.25 12l7.5-7.5', false);
})->with([
    'start' => ['home', 'd'],
    'threads' => ['threads.index', 'a'],
    'lists' => ['lists.index', 'c'],
    'bookmarks' => ['bookmarks.index', 'e'],
    'meals' => ['meals.index', 'f'],
    'account' => ['account', 'g'],
]);

it('paints a thread with the threads color', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('threads.show', Thread::factory()->create()))
        ->assertSuccessful()
        ->assertSee('data-section="a"', false)
        ->assertSee('data-color="a"', false)
        ->assertDontSee('--header-light', false)
        ->assertSee('href="'.route('threads.index').'"', false)
        ->assertSee('M15.75 19.5 8.25 12l7.5-7.5', false)
        ->assertSee('size-9', false)
        ->assertSee('border-current', false)
        ->assertDontSee('bg-[var(--section-mark)]', false);
});

it('paints list editing and user management with their section colors', function () {
    $admin = User::factory()->withRole(Role::Admin)->create();

    $this->actingAs($admin)
        ->get(route('lists.edit'))
        ->assertSuccessful()
        ->assertSee('data-section="c"', false)
        ->assertSee('data-color="c"', false)
        ->assertSee('href="'.route('lists.index').'"', false)
        ->assertSee('M15.75 19.5 8.25 12l7.5-7.5', false)
        ->assertSee('border-current text-[var(--section-mark)]', false);

    $this->actingAs($admin)
        ->get(route('users.index'))
        ->assertSuccessful()
        ->assertSee('data-section="g"', false)
        ->assertSee('data-color="g"', false)
        ->assertSee('href="'.route('account').'"', false)
        ->assertSee('M15.75 19.5 8.25 12l7.5-7.5', false)
        ->assertSee('aria-current="page"', false)
        ->assertSee('background-color: var(--color-d-1)', false);
});

it('marks the current section icon and leaves the others in their own color', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('lists.index'))
        ->assertSuccessful()
        ->assertSee('background-color: var(--color-d-1)', false)
        ->assertSee('background-color: var(--color-d-2)', false)
        ->assertSee('background-color: var(--color-a-2)', false)
        ->assertSee('background-color: var(--color-e-2)', false)
        ->assertSee('background-color: var(--color-f-2)', false)
        ->assertSee('background-color: var(--color-g-2)', false)
        ->assertDontSee('background-color: var(--color-c-2)', false)
        ->assertSee('aria-current="page"', false);

    $this->actingAs(User::factory()->create())
        ->get(route('threads.index'))
        ->assertSuccessful()
        ->assertSee('href="'.route('threads.index').'"', false)
        ->assertSee('href="'.route('home').'"', false)
        ->assertSee('background-color: var(--color-d-1)', false)
        ->assertSee('background-color: var(--color-d-2)', false)
        ->assertSee('background-color: var(--color-c-2)', false)
        ->assertSee('background-color: var(--color-e-2)', false)
        ->assertSee('background-color: var(--color-f-2)', false)
        ->assertSee('background-color: var(--color-g-2)', false)
        ->assertDontSee('background-color: var(--color-a-2)', false)
        ->assertSee('M20.25 8.511c.884.284', false)
        ->assertSee('aria-current="page"', false);

    $this->actingAs(User::factory()->create())
        ->get(route('home'))
        ->assertSuccessful()
        ->assertSee('background-color: var(--color-d-1)', false)
        ->assertSee('background-color: var(--color-a-2)', false)
        ->assertSee('background-color: var(--color-c-2)', false)
        ->assertSee('background-color: var(--color-e-2)', false)
        ->assertSee('background-color: var(--color-f-2)', false)
        ->assertSee('background-color: var(--color-g-2)', false)
        ->assertDontSee('background-color: var(--color-d-2)', false)
        ->assertSee('aria-label="'.__('home.title').'"', false)
        ->assertSee('aria-current="page"', false);
});

it('puts a start-colored home icon on the welcome header', function () {
    $this->get('/')
        ->assertSuccessful()
        ->assertDontSee(__('layout.brand'), false)
        ->assertSee('href="'.route('home').'"', false)
        ->assertSee('background-color: var(--color-d-2)', false)
        ->assertSee('m2.25 12 8.954-8.955', false)
        ->assertSee('href="'.route('login').'"', false)
        ->assertDontSee('M20.25 8.511c.884.284', false);

    $this->actingAs(User::factory()->create())
        ->get('/')
        ->assertSuccessful()
        ->assertDontSee(__('layout.brand'), false)
        ->assertSee('href="'.route('home').'"', false)
        ->assertSee('background-color: var(--color-d-2)', false)
        ->assertSee('aria-label="'.__('home.title').'"', false)
        ->assertDontSee('hover:underline">'.__('home.title'), false)
        ->assertDontSee('href="'.route('login').'"', false);
});

it('leaves the login header on the shared fallback', function () {
    $this->get(route('login'))
        ->assertSuccessful()
        ->assertSee('data-section="d"', false)
        ->assertSee('data-color="d"', false)
        ->assertDontSee('--header-light', false)
        ->assertSee('login-panel', false)
        ->assertSee('login-button', false)
        ->assertSee(__('layout.brand'), false)
        ->assertDontSee('bg-panel', false);
});
