<?php

use App\Enums\Feature;
use App\Enums\Role;
use App\Models\Context;
use App\Models\ItemList;
use App\Models\ListItem;
use App\Models\User;

it('asks guests to log in before list items', function () {
    $item = ListItem::factory()->create();

    $this->post(route('lists.items.store'), [
        'list_id' => $item->list_id,
        'item_name' => 'Skor',
    ])->assertRedirect(route('login'));

    $this->delete(route('lists.items.destroy', $item))
        ->assertRedirect(route('login'));

    $this->patch(route('lists.items.handle', $item))
        ->assertRedirect(route('login'));

    $this->get(route('lists.show', $item->itemList))
        ->assertRedirect(route('login'));
});

it('shows each list in its own table', function () {
    $user = User::factory()->withFeatures(Feature::PublishListItems)->create();
    $clothes = ItemList::factory()->for($user)->private()->create([
        'name' => 'Kläder',
        'context_id' => $user->currentContextId(),
    ]);
    $furniture = ItemList::factory()->for($user)->private()->create([
        'name' => 'Möbler',
        'context_id' => $user->currentContextId(),
    ]);
    ItemList::factory()->for($user)->private()->create([
        'name' => 'Väskor',
        'context_id' => $user->currentContextId(),
    ]);
    $books = ItemList::factory()->for($user)->private()->create([
        'name' => 'Böcker',
        'context_id' => $user->currentContextId(),
    ]);
    ListItem::factory()->for($books)->private()->create([
        'name' => 'Roman',
        'description' => null,
        'estimate' => null,
    ]);
    ListItem::factory()->for($furniture)->private()->create([
        'name' => 'Lampa',
        'description' => 'Vid fönstret',
        'estimate' => 400,
    ]);
    ListItem::factory()->for($clothes)->private()->create([
        'name' => 'Skor',
        'description' => null,
        'estimate' => 0,
    ]);
    ListItem::factory()->for($clothes)->private()->create([
        'name' => 'Mössa',
        'description' => null,
        'estimate' => null,
    ]);

    $response = $this->actingAs($user)
        ->get(route('lists.index'))
        ->assertSuccessful()
        ->assertSee(__('lists.description'))
        ->assertSee(__('lists.estimate'))
        ->assertSee('<th class="px-4 py-3 text-center"><span class="sr-only">'.__('lists.public').'</span></th>', false)
        ->assertDontSee('font-medium text-ink">'.__('lists.public'), false)
        ->assertSee(__('lists.items_empty'))
        ->assertSee('lists-surface', false)
        ->assertSee('lists-table', false)
        ->assertSee('lists-form', false)
        ->assertSee('x-data="handleItem"', false)
        ->assertSee('href="'.route('lists.show', $clothes).'"', false)
        ->assertSee(__('lists.handled_link'), false)
        ->assertSee('bg-[var(--color-c-5)]', false)
        ->assertDontSee('bg-form', false)
        ->assertSee('name="item_is_public" value="1"', false)
        ->assertDontSee('name="item_is_public" value="1" checked', false)
        ->assertSee('Skor')
        ->assertSee('Mössa')
        ->assertSee('Lampa')
        ->assertSee('Vid fönstret')
        ->assertSee('>400</td>', false)
        ->assertSee('>0</td>', false)
        ->assertDontSee('M12 21a9.004', false)
        ->assertSee('m14.74 9-.346 9', false)
        ->assertSee(__('lists.confirm_delete_item'), false);

    $html = $response->getContent();
    $empty = preg_quote(__('lists.items_empty'), '/');
    $estimate = preg_quote(__('lists.estimate'), '/');
    $publicHeader = '<th class="px-4 py-3 text-center"><span class="sr-only">'.__('lists.public').'</span></th>';

    expect($html)->toMatch('/<h2[^>]*>Böcker<\/h2>.*Roman.*<h2[^>]*>Kläder<\/h2>.*Mössa.*Skor.*<h2[^>]*>Möbler<\/h2>.*Lampa.*<h2[^>]*>Väskor<\/h2>.*'.$empty.'/s')
        ->and($html)->not->toMatch('/<h2[^>]*>Böcker<\/h2>(?:(?!<h2).)*'.$estimate.'/s')
        ->and($html)->toMatch('/<h2[^>]*>Kläder<\/h2>(?:(?!<h2).)*'.$estimate.'/s')
        ->and($html)->not->toMatch('/<h2[^>]*>Väskor<\/h2>(?:(?!<h2).)*'.$estimate.'/s')
        ->and(substr_count($html, $publicHeader))->toBe(4);
});

it('hides private items and items from lists the user cannot see', function () {
    $reader = User::factory()->create();
    $owner = User::factory()->create();
    $contextId = $reader->currentContextId();
    $elsewhere = Context::query()->create(['name' => 'Annan']);
    $shared = ItemList::factory()->for($owner)->create([
        'context_id' => $contextId,
        'name' => 'Delad',
    ]);
    $secretList = ItemList::factory()->for($owner)->private()->create([
        'context_id' => $contextId,
        'name' => 'Hemlig',
    ]);
    $otherContext = ItemList::factory()->for($owner)->create([
        'context_id' => $elsewhere->id,
        'name' => 'Annanstans',
    ]);

    ListItem::factory()->for($shared)->private()->create([
        'name' => 'Hemlig post',
    ]);
    ListItem::factory()->for($shared)->create([
        'name' => 'Synlig post',
        'description' => 'Alla får se',
        'estimate' => 90,
    ]);
    ListItem::factory()->for($secretList)->create([
        'name' => 'Dold lista',
    ]);
    ListItem::factory()->for($otherContext)->create([
        'name' => 'Annan kontext',
    ]);
    $open = ItemList::factory()->for($owner)->create([
        'context_id' => $contextId,
        'name' => 'Öppen',
    ]);
    ListItem::factory()->for($open)->private()->create([
        'name' => 'Osynlig',
    ]);

    $this->actingAs($reader)
        ->get(route('lists.index'))
        ->assertSuccessful()
        ->assertSee('Synlig post')
        ->assertSee('Alla får se')
        ->assertSee('90')
        ->assertSee('Delad')
        ->assertSee('Öppen')
        ->assertSee(__('lists.items_empty'))
        ->assertSee('M12 21a9.004', false)
        ->assertDontSee('Hemlig post')
        ->assertDontSee('Hemlig')
        ->assertDontSee('Dold lista')
        ->assertDontSee('Osynlig')
        ->assertDontSee('Annan kontext')
        ->assertDontSee('m14.74 9-.346 9', false)
        ->assertDontSee(__('lists.confirm_delete_item'), false);

    $this->actingAs($owner)
        ->get(route('lists.index'))
        ->assertSuccessful()
        ->assertSee('Hemlig post')
        ->assertSee('Dold lista')
        ->assertSee('Osynlig')
        ->assertDontSee('Annan kontext');
});

it('saves a private item on a visible list', function () {
    $user = User::factory()->create();
    $list = ItemList::factory()->for($user)->create([
        'name' => 'Kläder',
        'context_id' => $user->currentContextId(),
    ]);

    $this->actingAs($user)
        ->post(route('lists.items.store'), [
            'list_id' => $list->id,
            'item_name' => ' Skor ',
            'item_description' => '   ',
            'item_estimate' => '',
        ])
        ->assertRedirect(route('lists.index'))
        ->assertSessionHas('status', __('lists.item_created'));

    $item = ListItem::query()->sole();

    expect($item->name)->toBe('Skor')
        ->and($item->description)->toBeNull()
        ->and($item->estimate)->toBeNull()
        ->and($item->is_public)->toBeFalse()
        ->and($item->list_id)->toBe($list->id)
        ->and($item->handled_at)->toBeNull()
        ->and($item->handled_by)->toBeNull();

    $this->actingAs($user)
        ->get(route('lists.index'))
        ->assertSuccessful()
        ->assertSee('Skor')
        ->assertSee(__('lists.item_created'));
});

it('saves a public item with a description and estimate', function () {
    $user = User::factory()->withFeatures(Feature::PublishListItems)->create();
    $list = ItemList::factory()->for($user)->create([
        'context_id' => $user->currentContextId(),
    ]);

    $this->actingAs($user)
        ->post(route('lists.items.store'), [
            'list_id' => $list->id,
            'item_name' => 'Lampa',
            'item_description' => ' Vid fönstret ',
            'item_estimate' => '250',
            'item_is_public' => '1',
        ])
        ->assertRedirect(route('lists.index'));

    $item = ListItem::query()->sole();

    expect($item->description)->toBe('Vid fönstret')
        ->and($item->estimate)->toBe(250)
        ->and($item->is_public)->toBeTrue();
});

it('hides publishing an item from a member without that grant', function () {
    $member = User::factory()->create();
    $list = ItemList::factory()->for($member)->create([
        'context_id' => $member->currentContextId(),
    ]);

    $this->actingAs($member)
        ->get(route('lists.index'))
        ->assertSuccessful()
        ->assertDontSee('name="item_is_public"', false);

    $this->actingAs($member)
        ->post(route('lists.items.store'), [
            'list_id' => $list->id,
            'item_name' => 'Skor',
            'item_is_public' => '1',
        ])
        ->assertForbidden();

    expect(ListItem::query()->count())->toBe(0);
});

it('requires an item name, a whole estimate, and a visible list', function () {
    $user = User::factory()->create();
    $owner = User::factory()->create();
    $list = ItemList::factory()->for($user)->create([
        'context_id' => $user->currentContextId(),
    ]);
    $private = ItemList::factory()->for($owner)->private()->create([
        'context_id' => $user->currentContextId(),
        'name' => 'Hemlig',
    ]);

    $this->actingAs($user)
        ->post(route('lists.items.store'), [
            'list_id' => $list->id,
            'item_name' => '   ',
            'item_estimate' => '12.5',
        ])
        ->assertSessionHasErrors(['item_name', 'item_estimate']);

    $this->actingAs($user)
        ->post(route('lists.items.store'), [
            'list_id' => $private->id,
            'item_name' => 'Skor',
        ])
        ->assertSessionHasErrors(['list_id']);

    expect(ListItem::query()->count())->toBe(0);
});

it('deletes an item on a list the signed-in user created', function () {
    $user = User::factory()->create();
    $list = ItemList::factory()->for($user)->create([
        'name' => 'Kläder',
        'context_id' => $user->currentContextId(),
    ]);
    $item = ListItem::factory()->for($list)->create([
        'name' => 'Skor',
    ]);

    $this->actingAs($user)
        ->delete(route('lists.items.destroy', $item))
        ->assertRedirect(route('lists.index'))
        ->assertSessionHas('status', __('lists.item_deleted'));

    expect(ListItem::query()->find($item->id))->toBeNull();

    $this->actingAs($user)
        ->get(route('lists.index'))
        ->assertSuccessful()
        ->assertSee(__('lists.item_deleted'))
        ->assertDontSee('Skor');
});

it('refuses to delete an item on someone else\'s list', function () {
    $owner = User::factory()->create();
    $reader = User::factory()->create();
    $admin = User::factory()->withRole(Role::Admin)->create();
    $list = ItemList::factory()->for($owner)->create([
        'context_id' => $owner->currentContextId(),
    ]);
    $item = ListItem::factory()->for($list)->create([
        'name' => 'Skor',
    ]);

    $this->actingAs($reader)
        ->delete(route('lists.items.destroy', $item))
        ->assertForbidden();

    $this->actingAs($admin)
        ->delete(route('lists.items.destroy', $item))
        ->assertForbidden();

    expect(ListItem::query()->find($item->id))->not->toBeNull();
});

it('removes items when the list is deleted', function () {
    $user = User::factory()->withRole(Role::Admin)->create();
    $list = ItemList::factory()->for($user)->create([
        'context_id' => $user->currentContextId(),
    ]);
    $item = ListItem::factory()->for($list)->create();

    $this->actingAs($user)
        ->delete(route('lists.destroy', $list))
        ->assertRedirect(route('lists.edit'));

    expect(ListItem::query()->find($item->id))->toBeNull();
});

it('marks an item handled and keeps it on the list page', function () {
    $this->travelTo('2026-11-11 09:00:00');

    $user = User::factory()->create(['name' => 'Hanna']);
    $list = ItemList::factory()->for($user)->private()->create([
        'name' => 'Kläder',
        'context_id' => $user->currentContextId(),
    ]);
    ListItem::factory()->for($list)->private()->create([
        'name' => 'Zebra',
    ]);
    $done = ListItem::factory()->for($list)->private()->create([
        'name' => 'Skor',
        'description' => 'Vid dörren',
        'estimate' => 100,
    ]);

    $this->actingAs($user)
        ->patch(route('lists.items.handle', $done))
        ->assertNoContent();

    $done->refresh();

    expect($done->handled_by)->toBe($user->id)
        ->and($done->handled_at?->timezone(config('app.timezone'))->toDateString())->toBe('2026-11-11');

    $this->actingAs($user)
        ->get(route('lists.index'))
        ->assertSuccessful()
        ->assertSee('Zebra')
        ->assertDontSee('Skor')
        ->assertSee('href="'.route('lists.show', $list).'"', false);

    $response = $this->actingAs($user)
        ->get(route('lists.show', $list))
        ->assertSuccessful()
        ->assertSee(__('lists.handled_by'))
        ->assertSee(__('lists.handled_at'))
        ->assertSee('Zebra')
        ->assertSee('Skor')
        ->assertSee('Vid dörren')
        ->assertSee('Hanna')
        ->assertSee('2026-11-11')
        ->assertSee('>100</td>', false)
        ->assertSee('href="'.route('lists.index').'"', false);

    expect($response->getContent())->toMatch('/Zebra.*Skor/s');

    $this->actingAs($user)
        ->patch(route('lists.items.handle', $done))
        ->assertForbidden();
});

it('lets another member handle a public item', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $list = ItemList::factory()->for($owner)->create([
        'context_id' => $owner->currentContextId(),
    ]);
    $item = ListItem::factory()->for($list)->create([
        'name' => 'Lampa',
    ]);

    $this->actingAs($member)
        ->patch(route('lists.items.handle', $item))
        ->assertNoContent();

    expect($item->fresh()->handled_by)->toBe($member->id);
});

it('refuses to handle an item on a list the user cannot see', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $list = ItemList::factory()->for($owner)->private()->create([
        'context_id' => $owner->currentContextId(),
    ]);
    $item = ListItem::factory()->for($list)->create([
        'name' => 'Skor',
    ]);

    $this->actingAs($member)
        ->patch(route('lists.items.handle', $item))
        ->assertForbidden();

    $this->actingAs($member)
        ->get(route('lists.show', $list))
        ->assertForbidden();

    expect($item->fresh()->handled_at)->toBeNull();
});
