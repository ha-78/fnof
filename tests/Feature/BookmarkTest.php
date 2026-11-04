<?php

use App\Models\Bookmark;
use App\Models\Context;
use App\Models\User;

it('asks guests to log in before bookmarks', function () {
    $this->get(route('bookmarks.index'))
        ->assertRedirect(route('login'));

    $this->post(route('bookmarks.store'), [
        'name' => 'Recept',
        'url' => 'https://example.com/recept',
    ])->assertRedirect(route('login'));
});

it('lists a bookmark by name and full url', function () {
    $user = User::factory()->create();
    $createdAt = now()->timezone(config('app.timezone'))->setTime(15, 45);
    Bookmark::factory()->for($user)->create([
        'name' => 'Zebra',
        'url' => 'https://example.com/zebra',
        'created_at' => $createdAt,
    ]);
    Bookmark::factory()->for($user)->create([
        'name' => 'Apelsin',
        'url' => 'https://example.com/apelsin',
        'created_at' => $createdAt,
    ]);

    $response = $this->actingAs($user)
        ->get(route('bookmarks.index'))
        ->assertSuccessful()
        ->assertSee('data-table', false)
        ->assertSee(__('bookmarks.bookmarks'))
        ->assertSee(__('bookmarks.created_by'))
        ->assertSee(__('bookmarks.created_at'))
        ->assertSee('Apelsin')
        ->assertSee('Zebra')
        ->assertSee($user->name)
        ->assertSee($createdAt->toDateString())
        ->assertDontSee('15:45')
        ->assertDontSee('m8.5 12 2.25 2.25 4.75-5', false)
        ->assertSee('href="https://example.com/apelsin"', false)
        ->assertSee('href="https://example.com/zebra"', false)
        ->assertSee('https://example.com/apelsin')
        ->assertSee('https://example.com/zebra');

    expect($response->getContent())->toMatch('/Apelsin.*Zebra/s');
});

it('shows the bookmark link in the top row', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('home'))
        ->assertSuccessful()
        ->assertSee('href="'.route('bookmarks.index').'"', false)
        ->assertSee(__('bookmarks.bookmarks'), false);
});

it('hides private bookmarks and bookmarks from another context', function () {
    $reader = User::factory()->create();
    $owner = User::factory()->create();
    $contextId = $reader->currentContextId();
    $elsewhere = Context::query()->create(['name' => 'Annan']);

    Bookmark::factory()->for($owner)->create([
        'context_id' => $contextId,
        'name' => 'Hemlig',
        'url' => 'https://example.com/secret',
        'is_public' => false,
    ]);
    Bookmark::factory()->for($owner)->public()->create([
        'context_id' => $contextId,
        'name' => 'Delad',
        'url' => 'https://example.com/shared',
    ]);
    Bookmark::factory()->for($owner)->public()->create([
        'context_id' => $elsewhere->id,
        'name' => 'Annanstans',
        'url' => 'https://example.com/elsewhere',
    ]);

    $this->actingAs($reader)
        ->get(route('bookmarks.index'))
        ->assertSuccessful()
        ->assertSee('Delad')
        ->assertSee('https://example.com/shared')
        ->assertSee($owner->name)
        ->assertSee('m8.5 12 2.25 2.25 4.75-5', false)
        ->assertDontSee('Hemlig')
        ->assertDontSee('https://example.com/secret')
        ->assertDontSee('Annanstans')
        ->assertDontSee('https://example.com/elsewhere');
});

it('saves a private bookmark for the signed-in user', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('bookmarks.store'), [
            'name' => ' Recept ',
            'url' => ' https://example.com/recept ',
        ])
        ->assertRedirect(route('bookmarks.index'))
        ->assertSessionHas('status', __('bookmarks.created'));

    $bookmark = Bookmark::query()->sole();

    expect($bookmark->name)->toBe('Recept')
        ->and($bookmark->url)->toBe('https://example.com/recept')
        ->and($bookmark->is_public)->toBeFalse()
        ->and($bookmark->user_id)->toBe($user->id)
        ->and($bookmark->context_id)->toBe($user->currentContextId());

    $this->actingAs($user)
        ->get(route('bookmarks.index'))
        ->assertSuccessful()
        ->assertSee('Recept')
        ->assertSee('https://example.com/recept')
        ->assertSee(__('bookmarks.created'));
});

it('saves a public bookmark when asked', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('bookmarks.store'), [
            'name' => 'Karta',
            'url' => 'https://example.com/karta',
            'is_public' => '1',
        ])
        ->assertRedirect(route('bookmarks.index'));

    expect(Bookmark::query()->sole()->is_public)->toBeTrue();
});

it('requires a name and an http url', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('bookmarks.store'), [
            'name' => '   ',
            'url' => 'not a url',
        ])
        ->assertSessionHasErrors(['name', 'url']);

    expect(Bookmark::query()->count())->toBe(0);
});
