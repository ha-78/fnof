<?php

use App\Models\User;

it('shows the signed-in user on the home page', function () {
    $user = User::factory()->create(['name' => 'Hanna Anderberg']);

    $this->actingAs($user)
        ->get(route('home'))
        ->assertSuccessful()
        ->assertSee('Välkommen')
        ->assertSee('Hanna Anderberg');
});

it('asks guests to log in before home', function () {
    $this->get(route('home'))->assertRedirect(route('login'));
});
