<?php

use App\Models\User;

it('shows a status message in the current section colors', function () {
    $this->actingAs(User::factory()->create())
        ->withSession(['status' => 'Bokmärket är sparat.'])
        ->get(route('bookmarks.index'))
        ->assertSuccessful()
        ->assertSee('Bokmärket är sparat.')
        ->assertSee('role="status"', false)
        ->assertSee('bg-success', false)
        ->assertSee('data-color="e"', false)
        ->assertSee('aria-label="'.__('layout.close').'"', false)
        ->assertDontSee('bg-status', false);
});

it('shows an error message as an alert', function () {
    $this->actingAs(User::factory()->create())
        ->withSession(['error' => 'Det gick inte att spara.'])
        ->get(route('home'))
        ->assertSuccessful()
        ->assertSee('Det gick inte att spara.')
        ->assertSee('role="alert"', false)
        ->assertSee('bg-error', false);
});

it('marks a field with an error and links the message to it', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from(route('bookmarks.index'))
        ->post(route('bookmarks.store'), ['name' => '', 'url' => 'not a url'])
        ->assertRedirect(route('bookmarks.index'));

    $this->actingAs($user)
        ->get(route('bookmarks.index'))
        ->assertSee('aria-invalid="true"', false)
        ->assertSee('aria-describedby="url-error"', false)
        ->assertSee('id="url-error"', false)
        ->assertSee('border-field-error', false)
        ->assertDontSee('data-color="b"', false);
});

it('shows no message when there is nothing to say', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('home'))
        ->assertSuccessful()
        ->assertDontSee('role="status"', false)
        ->assertDontSee('role="alert"', false);
});
