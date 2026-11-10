<?php

use App\Models\User;

it('asks guests to log in before meals', function () {
    $this->get(route('meals.index'))
        ->assertRedirect(route('login'));
});

it('shows an empty meals page in the meals color', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('meals.index'))
        ->assertSuccessful()
        ->assertSee('--header-light: var(--color-f-3)', false)
        ->assertSee('--header-dark: var(--color-f-4)', false)
        ->assertSee('--canvas-dark: var(--color-f-5)', false)
        ->assertSee('--field-dark: var(--color-f-1)', false)
        ->assertSee('href="'.route('meals.index').'"', false)
        ->assertSee(__('meals.meals'), false)
        ->assertSee('M13 .5c0-.276-.226-.506-.498-.465', false)
        ->assertSee('background-color: var(--color-d-1)', false)
        ->assertDontSee('background-color: var(--color-f-2)', false)
        ->assertSee('aria-current="page"', false);
});
