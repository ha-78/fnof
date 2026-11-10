<?php

use App\Enums\Feature;
use App\Enums\Role;
use App\Models\Context;
use App\Models\ItemList;
use App\Models\User;

it('asks guests to log in before lists', function () {
    $this->get(route('lists.index'))
        ->assertRedirect(route('login'));

    $this->get(route('lists.edit'))
        ->assertRedirect(route('login'));

    $this->post(route('lists.store'), [
        'name' => 'Kläder',
    ])->assertRedirect(route('login'));

    $this->delete(route('lists.destroy', ItemList::factory()->create()))
        ->assertRedirect(route('login'));
});

it('shows the list editor to a member and an admin', function () {
    $admin = User::factory()->withRole(Role::Admin)->create();
    $god = User::factory()->withRole(Role::God)->create();
    $member = User::factory()->withRole(Role::Member)->create();
    $reader = User::factory()->withRole(Role::Reader)->create();

    $this->actingAs($admin)
        ->get(route('lists.index'))
        ->assertSuccessful()
        ->assertSee('href="'.route('lists.edit').'"', false)
        ->assertSee(__('lists.edit'), false);

    $this->actingAs($admin)
        ->get(route('lists.edit'))
        ->assertSuccessful()
        ->assertSee(__('lists.edit'))
        ->assertSee('name="is_public" value="1"', false)
        ->assertDontSee('name="is_public" value="1" checked', false);

    $this->actingAs($god)
        ->get(route('lists.edit'))
        ->assertSuccessful();

    $this->actingAs($reader)
        ->get(route('lists.index'))
        ->assertSuccessful()
        ->assertDontSee('href="'.route('lists.edit').'"', false);

    $this->actingAs($reader)
        ->get(route('lists.edit'))
        ->assertForbidden();

    $this->actingAs($member)
        ->get(route('lists.index'))
        ->assertSuccessful()
        ->assertSee('href="'.route('lists.edit').'"', false);

    $this->actingAs($member)
        ->get(route('lists.edit'))
        ->assertSuccessful()
        ->assertDontSee('name="is_public" value="1"', false)
        ->assertDontSee(__('lists.confirm_delete'), false);

    $this->actingAs($reader)
        ->post(route('lists.store'), [
            'name' => 'Kläder',
        ])
        ->assertForbidden();

    expect(ItemList::query()->count())->toBe(0);
});

it('lists a list by name', function () {
    $user = User::factory()->withRole(Role::Admin)->create();
    $createdAt = now()->timezone(config('app.timezone'))->setTime(15, 45);
    ItemList::factory()->for($user)->private()->create([
        'name' => 'Zebra',
        'context_id' => $user->currentContextId(),
        'created_at' => $createdAt,
    ]);
    ItemList::factory()->for($user)->private()->create([
        'name' => 'Apelsin',
        'context_id' => $user->currentContextId(),
        'created_at' => $createdAt,
    ]);

    $response = $this->actingAs($user)
        ->get(route('lists.edit'))
        ->assertSuccessful()
        ->assertSee('lists-surface', false)
        ->assertSee('lists-table', false)
        ->assertSee('lists-form', false)
        ->assertSee('bg-[var(--color-c-5)]', false)
        ->assertSee('text-[var(--color-c-1)]', false)
        ->assertDontSee('bg-form', false)
        ->assertSee(__('lists.edit'))
        ->assertSee(__('lists.created_by'))
        ->assertSee(__('lists.created_at'))
        ->assertSee('name="is_public" value="1"', false)
        ->assertDontSee('name="is_public" value="1" checked', false)
        ->assertSee('Apelsin')
        ->assertSee('Zebra')
        ->assertSee($user->name)
        ->assertSee($createdAt->toDateString())
        ->assertDontSee('15:45')
        ->assertDontSee('m4.5 12.75 6 6 9-13.5', false)
        ->assertSee('m14.74 9-.346 9', false)
        ->assertSee(__('lists.confirm_delete'), false);

    expect($response->getContent())->toMatch('/Apelsin.*Zebra/s');
});

it('shows the list link in the top row', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('home'))
        ->assertSuccessful()
        ->assertSee('href="'.route('lists.index').'"', false)
        ->assertSee('M8.25 6.75h12M8.25 12h12', false)
        ->assertSee(__('lists.lists'), false);
});

it('hides private lists and lists from another context', function () {
    $admin = User::factory()->withRole(Role::Admin)->create();
    $owner = User::factory()->create();
    $contextId = $admin->currentContextId();
    $elsewhere = Context::query()->create(['name' => 'Annan']);

    ItemList::factory()->for($owner)->private()->create([
        'context_id' => $contextId,
        'name' => 'Hemlig',
    ]);
    ItemList::factory()->for($owner)->create([
        'context_id' => $contextId,
        'name' => 'Delad',
    ]);
    ItemList::factory()->for($owner)->create([
        'context_id' => $elsewhere->id,
        'name' => 'Annanstans',
    ]);

    $this->actingAs($admin)
        ->get(route('lists.edit'))
        ->assertSuccessful()
        ->assertSee('Delad')
        ->assertSee($owner->name)
        ->assertSee('m4.5 12.75 6 6 9-13.5', false)
        ->assertDontSee('Hemlig')
        ->assertDontSee('Annanstans');
});

it('saves a private list for a member', function () {
    $member = User::factory()->withRole(Role::Member)->create();

    $this->actingAs($member)
        ->post(route('lists.store'), [
            'name' => 'Kläder',
        ])
        ->assertRedirect(route('lists.edit'));

    expect(ItemList::query()->sole()->is_public)->toBeFalse();
});

it('saves a private list for the signed-in admin', function () {
    $user = User::factory()->withRole(Role::Admin)->create();

    $this->actingAs($user)
        ->post(route('lists.store'), [
            'name' => ' Kläder ',
        ])
        ->assertRedirect(route('lists.edit'))
        ->assertSessionHas('status', __('lists.created'));

    $list = ItemList::query()->sole();

    expect($list->name)->toBe('Kläder')
        ->and($list->is_public)->toBeFalse()
        ->and($list->user_id)->toBe($user->id)
        ->and($list->context_id)->toBe($user->currentContextId());

    $this->actingAs($user)
        ->get(route('lists.edit'))
        ->assertSuccessful()
        ->assertSee('Kläder')
        ->assertSee(__('lists.created'));
});

it('saves a public list when asked', function () {
    $user = User::factory()->withRole(Role::Admin)->create();

    $this->actingAs($user)
        ->post(route('lists.store'), [
            'name' => 'Möbler',
            'is_public' => '1',
        ])
        ->assertRedirect(route('lists.edit'));

    expect(ItemList::query()->sole()->is_public)->toBeTrue();
});

it('refuses to publish a list without that grant', function () {
    $member = User::factory()->withRole(Role::Member)->create();

    $this->actingAs($member)
        ->post(route('lists.store'), [
            'name' => 'Kläder',
            'is_public' => '1',
        ])
        ->assertForbidden();

    expect(ItemList::query()->count())->toBe(0);
});

it('lets a member with the grant publish a list', function () {
    $member = User::factory()->withFeatures(Feature::PublishLists)->create();

    $this->actingAs($member)
        ->get(route('lists.edit'))
        ->assertSuccessful()
        ->assertSee('name="is_public" value="1"', false)
        ->assertDontSee('name="is_public" value="1" checked', false);

    $this->actingAs($member)
        ->post(route('lists.store'), [
            'name' => 'Möbler',
            'is_public' => '1',
        ])
        ->assertRedirect(route('lists.edit'));

    expect(ItemList::query()->sole()->is_public)->toBeTrue();
});

it('requires a name', function () {
    $this->actingAs(User::factory()->withRole(Role::Admin)->create())
        ->post(route('lists.store'), [
            'name' => '   ',
        ])
        ->assertSessionHasErrors(['name']);

    expect(ItemList::query()->count())->toBe(0);
});

it('deletes a list in this context', function () {
    $user = User::factory()->withRole(Role::Admin)->create();
    $owner = User::factory()->create();
    $list = ItemList::factory()->for($owner)->create([
        'name' => 'Kläder',
        'context_id' => $user->currentContextId(),
    ]);

    $this->actingAs($user)
        ->delete(route('lists.destroy', $list))
        ->assertRedirect(route('lists.edit'))
        ->assertSessionHas('status', __('lists.deleted'));

    expect(ItemList::query()->find($list->id))->toBeNull();

    $this->actingAs($user)
        ->get(route('lists.edit'))
        ->assertSuccessful()
        ->assertSee(__('lists.deleted'))
        ->assertDontSee('Kläder');
});

it('refuses to delete a list outside this context or below admin', function () {
    $owner = User::factory()->create();
    $reader = User::factory()->create();
    $member = User::factory()->withRole(Role::Member)->create();
    $admin = User::factory()->withRole(Role::Admin)->create();
    $elsewhere = Context::query()->create(['name' => 'Annan']);
    $list = ItemList::factory()->for($owner)->create([
        'context_id' => $owner->currentContextId(),
        'name' => 'Delad',
    ]);
    $other = ItemList::factory()->for($owner)->create([
        'context_id' => $elsewhere->id,
        'name' => 'Annanstans',
    ]);

    $this->actingAs($reader)
        ->delete(route('lists.destroy', $list))
        ->assertForbidden();

    $this->actingAs($member)
        ->delete(route('lists.destroy', $list))
        ->assertForbidden();

    $this->actingAs($admin)
        ->delete(route('lists.destroy', $other))
        ->assertForbidden();

    expect(ItemList::query()->find($list->id))->not->toBeNull()
        ->and(ItemList::query()->find($other->id))->not->toBeNull();
});
