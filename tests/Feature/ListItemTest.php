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
});

it('lists items by list name then item name', function () {
    $user = User::factory()->withFeatures(Feature::PublishListItems)->create();
    $clothes = ItemList::factory()->for($user)->private()->create([
        'name' => 'Kläder',
        'context_id' => $user->currentContextId(),
    ]);
    $furniture = ItemList::factory()->for($user)->private()->create([
        'name' => 'Möbler',
        'context_id' => $user->currentContextId(),
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

    $response = $this->actingAs($user)
        ->get(route('lists.index'))
        ->assertSuccessful()
        ->assertSee(__('lists.items'))
        ->assertSee(__('lists.description'))
        ->assertSee(__('lists.estimate'))
        ->assertSee('lists-surface', false)
        ->assertSee('lists-table', false)
        ->assertSee('lists-form', false)
        ->assertSee('bg-[var(--color-c-5)]', false)
        ->assertDontSee('bg-form', false)
        ->assertSee('name="item_is_public" value="1"', false)
        ->assertDontSee('name="item_is_public" value="1" checked', false)
        ->assertSee('Skor')
        ->assertSee('Lampa')
        ->assertSee('Vid fönstret')
        ->assertSee('>400</td>', false)
        ->assertSee('>0</td>', false)
        ->assertDontSee('m4.5 12.75 6 6 9-13.5', false)
        ->assertSee('m14.74 9-.346 9', false)
        ->assertSee(__('lists.confirm_delete_item'), false);

    expect($response->getContent())->toMatch('/Skor.*Lampa/s');
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

    $this->actingAs($reader)
        ->get(route('lists.index'))
        ->assertSuccessful()
        ->assertSee('Synlig post')
        ->assertSee('Alla får se')
        ->assertSee('90')
        ->assertSee('Delad')
        ->assertSee('m4.5 12.75 6 6 9-13.5', false)
        ->assertDontSee('Hemlig post')
        ->assertDontSee('Hemlig')
        ->assertDontSee('Dold lista')
        ->assertDontSee('Annan kontext')
        ->assertDontSee('m14.74 9-.346 9', false)
        ->assertDontSee(__('lists.confirm_delete_item'), false);

    $this->actingAs($owner)
        ->get(route('lists.index'))
        ->assertSuccessful()
        ->assertSee('Hemlig post')
        ->assertSee('Dold lista')
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
