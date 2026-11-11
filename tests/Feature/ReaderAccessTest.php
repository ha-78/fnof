<?php

use App\Enums\Feature;
use App\Enums\Role;
use App\Models\Bookmark;
use App\Models\ItemList;
use App\Models\ListItem;
use App\Models\Meal;
use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;

it('hides writing forms from a reader and refuses the posts', function () {
    $reader = User::factory()->withRole(Role::Reader)->create();
    $contextId = $reader->currentContextId();
    $thread = Thread::factory()->for($reader)->create([
        'context_id' => $contextId,
        'body' => 'En tråd',
    ]);
    $reply = Reply::factory()->for($thread)->for($reader)->create([
        'body' => 'Ett svar',
    ]);
    $bookmark = Bookmark::factory()->for($reader)->create([
        'context_id' => $contextId,
        'name' => 'Recept',
        'url' => 'https://example.com/recept',
    ]);
    $list = ItemList::factory()->for($reader)->create([
        'context_id' => $contextId,
        'name' => 'Kläder',
    ]);
    $item = ListItem::factory()->for($list)->create([
        'name' => 'Skor',
    ]);
    Meal::factory()->for($reader)->create([
        'name' => 'Pasta',
    ]);

    $this->actingAs($reader)
        ->get(route('home'))
        ->assertSuccessful()
        ->assertSee('En tråd')
        ->assertDontSee('threads-form', false)
        ->assertDontSee(__('threads.publish'));

    $this->actingAs($reader)
        ->get(route('threads.index'))
        ->assertSuccessful()
        ->assertDontSee('threads-form', false);

    $this->actingAs($reader)
        ->get(route('threads.show', $thread))
        ->assertSuccessful()
        ->assertSee('En tråd')
        ->assertSee('Ett svar')
        ->assertDontSee('action="'.route('threads.replies.store', $thread).'"', false)
        ->assertDontSee('action="'.route('threads.update', $thread).'"', false)
        ->assertDontSee('action="'.route('threads.archive', $thread).'"', false)
        ->assertDontSee('action="'.route('threads.destroy', $thread).'"', false)
        ->assertDontSee('action="'.route('replies.destroy', [$thread, $reply]).'"', false);

    $this->actingAs($reader)
        ->get(route('bookmarks.index'))
        ->assertSuccessful()
        ->assertSee('Recept')
        ->assertDontSee('bookmarks-form', false)
        ->assertDontSee('action="'.route('bookmarks.destroy', $bookmark).'"', false);

    $this->actingAs($reader)
        ->get(route('lists.index'))
        ->assertSuccessful()
        ->assertSee('Kläder')
        ->assertSee('Skor')
        ->assertDontSee('lists-form', false)
        ->assertDontSee('action="'.route('lists.items.destroy', $item).'"', false)
        ->assertDontSee('data-handle-url="'.route('lists.items.handle', $item).'"', false);

    $this->actingAs($reader)
        ->get(route('meals.index'))
        ->assertSuccessful()
        ->assertSee('Pasta')
        ->assertDontSee('meals-form', false)
        ->assertDontSee(__('meals.prompt'), false);

    $this->actingAs($reader)
        ->get(route('account'))
        ->assertSuccessful()
        ->assertSee(__('themes.label'))
        ->assertSee('action="'.route('logout').'"', false)
        ->assertSee(__('users.log_out'));

    $this->actingAs($reader)
        ->post(route('threads.store'), ['body' => 'Nej'])
        ->assertForbidden();

    $this->actingAs($reader)
        ->post(route('threads.replies.store', $thread), ['body' => 'Nej'])
        ->assertForbidden();

    $this->actingAs($reader)
        ->patch(route('threads.update', $thread), ['title' => 'Nej'])
        ->assertForbidden();

    $this->actingAs($reader)
        ->patch(route('threads.archive', $thread))
        ->assertForbidden();

    $this->actingAs($reader)
        ->delete(route('threads.destroy', $thread))
        ->assertForbidden();

    $this->actingAs($reader)
        ->delete(route('replies.destroy', [$thread, $reply]))
        ->assertForbidden();

    $this->actingAs($reader)
        ->post(route('bookmarks.store'), [
            'name' => 'Ny',
            'url' => 'https://example.com/ny',
        ])
        ->assertForbidden();

    $this->actingAs($reader)
        ->delete(route('bookmarks.destroy', $bookmark))
        ->assertForbidden();

    $this->actingAs($reader)
        ->post(route('lists.items.store'), [
            'list_id' => $list->id,
            'item_name' => 'Ny',
        ])
        ->assertForbidden();

    $this->actingAs($reader)
        ->delete(route('lists.items.destroy', $item))
        ->assertForbidden();

    $this->actingAs($reader)
        ->patch(route('lists.items.handle', $item))
        ->assertForbidden();

    $this->actingAs($reader)
        ->post(route('meals.store'), ['name' => 'Ny'])
        ->assertForbidden();

    expect($thread->fresh()->title)->toBeNull()
        ->and($thread->fresh()->archived_at)->toBeNull()
        ->and(Thread::query()->find($thread->id))->not->toBeNull()
        ->and(Reply::query()->find($reply->id))->not->toBeNull()
        ->and(Bookmark::query()->where('name', 'Ny')->exists())->toBeFalse()
        ->and(ListItem::query()->where('name', 'Ny')->exists())->toBeFalse()
        ->and(Meal::query()->where('name', 'Ny')->exists())->toBeFalse();
});

it('shows the thread form to a member who may create threads', function () {
    $member = User::factory()->withRole(Role::Member)->withFeatures(Feature::CreateThreads)->create();

    $this->actingAs($member)
        ->get(route('home'))
        ->assertSuccessful()
        ->assertSee('threads-form', false)
        ->assertSee(__('threads.publish'));
});
