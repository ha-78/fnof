<?php

use App\Models\User;

it('shows the signed-in user on the home page', function () {
    $user = User::factory()->create(['name' => 'Hanna Anderberg']);

    $this->actingAs($user)
        ->get(route('home'))
        ->assertSuccessful()
        ->assertSee('Välkommen')
        ->assertSee('Hanna Anderberg')
        ->assertSee(route('account'), false)
        ->assertSee(__('users.account'), false)
        ->assertDontSee(__('users.log_out'))
        ->assertDontSee(__('themes.label'));
});

it('asks guests to log in before home', function () {
    $this->get(route('home'))->assertRedirect(route('login'));
});

it('shows today, tomorrow, and the eight days after', function () {
    $this->travelTo('2026-10-27 15:00:00');

    $today = now()->timezone(config('app.timezone'))->locale(config('app.locale'));
    $tomorrow = $today->copy()->addDay();
    $upcoming = collect(range(2, 9))->map(fn (int $offset) => $today->copy()->addDays($offset));
    $later = $today->copy()->addDays(5);

    $this->actingAs(User::factory()->create())
        ->get(route('home'))
        ->assertSuccessful()
        ->assertSeeInOrder([
            __('calendar.today'),
            $today->isoFormat('dddd'),
            $today->toDateString(),
            $today->isoFormat('D'),
            $today->isoFormat('MMMM'),
            __('calendar.tomorrow'),
            $tomorrow->isoFormat('dddd'),
            $tomorrow->toDateString(),
            $tomorrow->isoFormat('D'),
            $tomorrow->isoFormat('MMMM'),
            ...$upcoming->flatMap(fn ($date) => $date->toDateString() === $later->toDateString()
                ? [$date->toDateString(), $date->isoFormat('dddd'), $date->isoFormat('D'), $date->isoFormat('MMMM')]
                : [$date->toDateString()])->all(),
        ])
        ->assertSee('bg-calendar', false)
        ->assertSee('text-calendar-ink', false);
});
