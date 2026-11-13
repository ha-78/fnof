<?php

use App\Enums\Feature;
use App\Enums\Role;
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

    $this->delete(route('bookmarks.destroy', Bookmark::factory()->create()))
        ->assertRedirect(route('login'));
});

it('lists a bookmark by name, site and link', function () {
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
        ->assertDontSee('var(--color-', false)
        ->assertSee(__('bookmarks.bookmarks'))
        ->assertSee(__('bookmarks.new'))
        ->assertSee(__('bookmarks.private'))
        ->assertDontSee(__('bookmarks.shared'))
        ->assertSee('>https://example.com/apelsin</span>', false)
        ->assertSee('Apelsin')
        ->assertSee('Zebra')
        ->assertSee($user->name)
        ->assertSee($createdAt->toDateString())
        ->assertDontSee('15:45')
        ->assertDontSee('M12 21a9.004', false)
        ->assertSee('m14.74 9-.346 9', false)
        ->assertSee(__('bookmarks.confirm_delete'), false)
        ->assertSee('href="https://example.com/apelsin"', false)
        ->assertSee('href="https://example.com/zebra"', false)
        ->assertSee('target="_blank" rel="noopener noreferrer"', false);

    expect($response->getContent())->toMatch('/Apelsin.*Zebra/s');
});

it('lists shared bookmarks before the user\'s private ones', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    Bookmark::factory()->for($user)->create([
        'name' => 'Min egen',
        'url' => 'https://www.example.com/egen',
    ]);
    Bookmark::factory()->for($other)->public()->create([
        'context_id' => $user->currentContextId(),
        'name' => 'Familjens',
        'url' => 'https://example.org/familj',
    ]);

    $response = $this->actingAs($user)
        ->get(route('bookmarks.index'))
        ->assertSuccessful()
        ->assertSee(__('bookmarks.shared'))
        ->assertSee(__('bookmarks.private'))
        ->assertSee('>https://example.org/familj</span>', false)
        ->assertSee('>https://www.example.com/egen</span>', false);

    expect($response->getContent())->toMatch('/'.preg_quote(__('bookmarks.shared'), '/').'.*Familjens.*'.preg_quote(__('bookmarks.private'), '/').'.*Min egen/s');
});

it('opens the new bookmark sheet again when saving fails', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('bookmarks.index'))
        ->assertSee('x-data="{ open: false }"', false)
        ->assertSee("x-on:click=\"\$dispatch('open-sheet', 'new-bookmark')\"", false);

    $this->actingAs($user)
        ->from(route('bookmarks.index'))
        ->post(route('bookmarks.store'), ['name' => '', 'url' => ''])
        ->assertRedirect(route('bookmarks.index'));

    $this->actingAs($user)
        ->get(route('bookmarks.index'))
        ->assertSee('x-data="{ open: true }"', false);
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
        ->assertDontSee('M12 21a9.004', false)
        ->assertDontSee('Hemlig')
        ->assertDontSee('https://example.com/secret')
        ->assertDontSee('Annanstans')
        ->assertDontSee('https://example.com/elsewhere')
        ->assertDontSee('m14.74 9-.346 9', false)
        ->assertDontSee(__('bookmarks.confirm_delete'), false);
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
    $user = User::factory()->withFeatures(Feature::PublishBookmarks)->create();

    $this->actingAs($user)
        ->get(route('bookmarks.index'))
        ->assertSuccessful()
        ->assertSee('name="is_public" value="1"', false)
        ->assertDontSee('name="is_public" value="1" checked', false);

    $this->actingAs($user)
        ->post(route('bookmarks.store'), [
            'name' => 'Karta',
            'url' => 'https://example.com/karta',
            'is_public' => '1',
        ])
        ->assertRedirect(route('bookmarks.index'));

    expect(Bookmark::query()->sole()->is_public)->toBeTrue();
});

it('hides publishing a bookmark from a member without that grant', function () {
    $member = User::factory()->create();

    $this->actingAs($member)
        ->get(route('bookmarks.index'))
        ->assertSuccessful()
        ->assertSee('action="'.route('bookmarks.store').'"', false)
        ->assertDontSee('name="is_public"', false);

    $this->actingAs($member)
        ->post(route('bookmarks.store'), [
            'name' => 'Karta',
            'url' => 'https://example.com/karta',
            'is_public' => '1',
        ])
        ->assertForbidden();

    expect(Bookmark::query()->count())->toBe(0);
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

it('deletes a bookmark the signed-in user created', function () {
    $user = User::factory()->create();
    $bookmark = Bookmark::factory()->for($user)->create([
        'name' => 'Recept',
        'url' => 'https://example.com/recept',
        'context_id' => $user->currentContextId(),
    ]);

    $this->actingAs($user)
        ->delete(route('bookmarks.destroy', $bookmark))
        ->assertRedirect(route('bookmarks.index'))
        ->assertSessionHas('status', __('bookmarks.deleted'));

    expect(Bookmark::query()->find($bookmark->id))->toBeNull();

    $this->actingAs($user)
        ->get(route('bookmarks.index'))
        ->assertSuccessful()
        ->assertSee(__('bookmarks.deleted'))
        ->assertDontSee('Recept');
});

it('refuses to delete a bookmark someone else created', function () {
    $owner = User::factory()->create();
    $reader = User::factory()->create();
    $admin = User::factory()->withRole(Role::Admin)->create();
    $bookmark = Bookmark::factory()->for($owner)->public()->create([
        'context_id' => $owner->currentContextId(),
        'name' => 'Delad',
        'url' => 'https://example.com/shared',
    ]);

    $this->actingAs($reader)
        ->delete(route('bookmarks.destroy', $bookmark))
        ->assertForbidden();

    $this->actingAs($admin)
        ->delete(route('bookmarks.destroy', $bookmark))
        ->assertForbidden();

    expect(Bookmark::query()->find($bookmark->id))->not->toBeNull();
});
