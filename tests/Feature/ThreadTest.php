<?php

use App\Enums\Role;
use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use App\Services\ThreadService;

it('starts a thread from a message without a name', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post(route('threads.store'), ['body' => 'En kort tråd']);

    $thread = Thread::query()->sole();

    $response->assertRedirect(route('threads.show', $thread))
        ->assertSessionHas('status', __('threads.created'));

    expect($thread->title)->toBeNull()
        ->and($thread->body)->toBe('En kort tråd')
        ->and($thread->user_id)->toBe($user->id);

    $this->actingAs($user)
        ->get(route('home'))
        ->assertSuccessful()
        ->assertSee('En kort tråd')
        ->assertSee($user->name);
});

it('asks guests to log in before a thread', function () {
    $this->post(route('threads.store'), ['body' => 'Hej'])
        ->assertRedirect(route('login'));

    $this->get(route('threads.index'))
        ->assertRedirect(route('login'));

    $this->get(route('threads.show', Thread::factory()->create()))
        ->assertRedirect(route('login'));

    $this->get(route('home'), ['X-Fragment' => 'thread-list'])
        ->assertRedirect(route('login'));

    $this->getJson(route('threads.version'))
        ->assertRedirect(route('login'));
});

it('shows thread dates as today, yesterday, or days ago', function () {
    $reader = User::factory()->create();
    $thread = Thread::factory()->create([
        'body' => 'Gammal tråd',
        'created_at' => now()->subDays(5),
    ]);
    Reply::factory()->for($thread)->create([
        'body' => 'Gårdagens svar',
        'created_at' => now()->subDay(),
    ]);
    $postedAt = $thread->created_at->timezone(config('app.timezone'))->format('Y-m-d H:i');
    $daysAgo = trans_choice('threads.days_ago', 5, ['count' => 5]);

    $this->actingAs($reader)
        ->get(route('home'))
        ->assertSuccessful()
        ->assertSee(__('threads.yesterday'))
        ->assertSee($daysAgo)
        ->assertDontSee($postedAt)
        ->assertSee('data-relative', false)
        ->assertSee('data-posted-today="'.__('threads.today').'"', false)
        ->assertSee('data-posted-days-ago="'.__('threads.days_ago_phrase').'"', false);

    $this->actingAs($reader)
        ->get(route('threads.show', $thread))
        ->assertSuccessful()
        ->assertSee($postedAt)
        ->assertDontSee($daysAgo)
        ->assertDontSee('data-relative', false);
});

it('lists threads on the threads page', function () {
    $user = User::factory()->create();
    Thread::factory()->create(['body' => 'En tråd i listan']);

    $this->actingAs($user)
        ->get(route('threads.index'))
        ->assertSuccessful()
        ->assertSee('En tråd i listan')
        ->assertSee(__('threads.threads'))
        ->assertSee(__('threads.archive'));
});

it('requires a message to start a thread', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('threads.store'), ['body' => '   '])
        ->assertSessionHasErrors('body');

    expect(Thread::query()->count())->toBe(0);
});

it('rejects images in a thread', function (string $body) {
    $this->actingAs(User::factory()->create())
        ->post(route('threads.store'), ['body' => $body])
        ->assertSessionHasErrors([
            'body' => __('threads.images_not_allowed'),
        ]);

    expect(Thread::query()->count())->toBe(0);
})->with([
    'img tag' => '<img src="https://example.com/a.png">',
    'markdown' => 'Se ![bild](https://example.com/a.png)',
    'data uri' => 'data:image/png;base64,aaaa',
]);

it('keeps a picture link as text', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('threads.store'), ['body' => 'https://example.com/photo.png'])
        ->assertRedirect();

    expect(Thread::query()->sole()->body)->toBe('https://example.com/photo.png');
});

it('lists newer open threads first and keeps archived threads in the archive', function () {
    $reader = User::factory()->create();
    Thread::factory()->create([
        'body' => 'Äldre tråd',
        'created_at' => now()->subDay(),
    ]);
    Thread::factory()->create([
        'body' => 'Nyare tråd',
    ]);
    Thread::factory()->archived()->create([
        'body' => 'Arkiverad tråd',
    ]);

    $this->actingAs($reader)
        ->get(route('home'))
        ->assertSuccessful()
        ->assertSeeInOrder(['Nyare tråd', 'Äldre tråd'])
        ->assertDontSee('Arkiverad tråd');

    $this->actingAs($reader)
        ->get(route('home', ['archived' => 1]))
        ->assertSuccessful()
        ->assertSee('Arkiverad tråd')
        ->assertDontSee('Nyare tråd');
});

it('lists a thread with a newer reply ahead of a newer thread', function () {
    $reader = User::factory()->create();
    $older = Thread::factory()->create([
        'body' => 'Äldre tråd',
        'created_at' => now()->subDays(2),
    ]);
    Thread::factory()->create([
        'body' => 'Nyare tråd',
        'created_at' => now()->subDay(),
    ]);
    $quiet = Thread::factory()->create([
        'body' => 'Tyst tråd',
        'created_at' => now()->subDays(3),
    ]);
    Reply::factory()->for($older)->create([
        'body' => 'Nytt svar',
        'created_at' => now(),
    ]);
    $removed = Reply::factory()->for($quiet)->create([
        'body' => 'Borttaget svar',
        'created_at' => now(),
    ]);
    $removed->delete();

    $this->actingAs($reader)
        ->get(route('home'))
        ->assertSuccessful()
        ->assertSeeInOrder(['Äldre tråd', 'Nyare tråd', 'Tyst tråd'])
        ->assertDontSee('Borttaget svar');
});

it('serves a live thread list without the rest of the page', function () {
    $reader = User::factory()->create();
    $older = Thread::factory()->create([
        'body' => 'Äldre tråd',
        'created_at' => now()->subDays(2),
    ]);
    Thread::factory()->create([
        'body' => 'Nyare tråd',
        'created_at' => now()->subDay(),
    ]);
    Thread::factory()->archived()->create([
        'body' => 'Arkiverad tråd',
    ]);
    Reply::factory()->for($older)->create(['created_at' => now()]);

    $this->actingAs($reader)
        ->get(route('home'), ['X-Fragment' => 'thread-list'])
        ->assertSuccessful()
        ->assertSeeInOrder(['Äldre tråd', 'Nyare tråd'])
        ->assertDontSee('Arkiverad tråd')
        ->assertDontSee(__('threads.publish'));

    $this->actingAs($reader)
        ->get(route('threads.index', ['archived' => 1]), ['X-Fragment' => 'thread-list'])
        ->assertSuccessful()
        ->assertSee('Arkiverad tråd')
        ->assertDontSee('Nyare tråd')
        ->assertDontSee(__('threads.publish'));
});

it('serves live replies without the rest of the thread page', function () {
    $thread = Thread::factory()->create(['body' => 'Startmeddelandet']);
    Reply::factory()->for($thread)->create(['body' => 'Första svaret']);
    $removed = Reply::factory()->for($thread)->create(['body' => 'Borttaget svar']);
    $removed->delete();

    $this->actingAs($thread->user)
        ->get(route('threads.show', $thread), ['X-Fragment' => 'replies'])
        ->assertSuccessful()
        ->assertSee('Första svaret')
        ->assertDontSee('Borttaget svar')
        ->assertDontSee('Startmeddelandet')
        ->assertDontSee(__('threads.name'))
        ->assertSee('data-archived="0"', false);

    $thread->forceFill(['archived_at' => now()])->save();

    $this->actingAs($thread->user)
        ->get(route('threads.show', $thread), ['X-Fragment' => 'replies'])
        ->assertSuccessful()
        ->assertSee(__('threads.archived_notice'))
        ->assertSee('data-archived="1"', false);
});

it('bumps one shared version when a thread changes', function () {
    $thread = Thread::factory()->create();
    $reply = Reply::factory()->for($thread)->create();
    $before = app(ThreadService::class)->version();

    $this->actingAs(User::factory()->create())
        ->post(route('threads.replies.store', $thread), ['body' => 'Ett svar'])
        ->assertRedirect();

    expect(app(ThreadService::class)->version())->toBe($before + 1);

    $this->actingAs($reply->user)
        ->delete(route('replies.destroy', [$thread, $reply]))
        ->assertRedirect();

    expect(app(ThreadService::class)->version())->toBe($before + 2);

    $this->actingAs(User::factory()->create())
        ->getJson(route('threads.version'))
        ->assertSuccessful()
        ->assertJson(['version' => $before + 2]);
});

it('leaves the shared version unchanged when a reply is refused', function () {
    $thread = Thread::factory()->archived()->create();
    $before = app(ThreadService::class)->version();

    $this->actingAs(User::factory()->create())
        ->post(route('threads.replies.store', $thread), ['body' => 'För sent'])
        ->assertSessionHasErrors('body');

    expect(app(ThreadService::class)->version())->toBe($before);
});

it('misses a live thread that was deleted', function () {
    $thread = Thread::factory()->create();
    $thread->delete();

    $this->actingAs(User::factory()->create())
        ->get(route('threads.show', $thread), ['X-Fragment' => 'replies'])
        ->assertNotFound();
});

it('shows every reply under its thread on the home page', function () {
    $thread = Thread::factory()->create(['body' => 'Startmeddelandet']);
    Reply::factory()->for($thread)->create(['body' => 'Första svaret']);
    Reply::factory()->for($thread)->create(['body' => 'Andra svaret']);
    $removed = Reply::factory()->for($thread)->create(['body' => 'Borttaget svar']);
    $removed->delete();

    $this->actingAs(User::factory()->create())
        ->get(route('home'))
        ->assertSuccessful()
        ->assertSee('Startmeddelandet')
        ->assertSeeInOrder(['Första svaret', 'Andra svaret'])
        ->assertDontSee('Borttaget svar');
});

it('shows a thread and its replies', function () {
    $thread = Thread::factory()->create(['body' => 'Startmeddelandet']);
    Reply::factory()->for($thread)->create(['body' => 'Första svaret']);

    $this->actingAs(User::factory()->create())
        ->get(route('threads.show', $thread))
        ->assertSuccessful()
        ->assertSee('Startmeddelandet')
        ->assertSee('Första svaret')
        ->assertSee(__('threads.reply'));
});

it('lets another user reply', function () {
    $thread = Thread::factory()->create(['body' => 'Start']);
    $other = User::factory()->create();

    $this->actingAs($other)
        ->post(route('threads.replies.store', $thread), ['body' => 'Ett svar'])
        ->assertRedirect(route('threads.show', $thread))
        ->assertSessionHas('status', __('threads.replied'));

    $reply = $thread->replies()->sole();

    expect($reply->body)->toBe('Ett svar')
        ->and($reply->user_id)->toBe($other->id);

    $this->actingAs($other)
        ->get(route('home'))
        ->assertSee('Start')
        ->assertSee('Ett svar');
});

it('refuses a reply on an archived thread', function () {
    $thread = Thread::factory()->archived()->create();

    $this->actingAs(User::factory()->create())
        ->post(route('threads.replies.store', $thread), ['body' => 'För sent'])
        ->assertSessionHasErrors([
            'body' => __('threads.archived_no_replies'),
        ]);

    expect($thread->replies()->count())->toBe(0);

    $this->actingAs($thread->user)
        ->get(route('threads.show', $thread))
        ->assertSuccessful()
        ->assertSee(__('threads.archived_notice'))
        ->assertSee('data-archived="1"', false)
        ->assertSee('x-cloak', false);
});

it('lets the author name a thread and clear the name', function () {
    $thread = Thread::factory()->create(['body' => 'Start']);

    $this->actingAs($thread->user)
        ->patch(route('threads.update', $thread), ['title' => '  Långlivad  '])
        ->assertRedirect(route('threads.show', $thread));

    expect($thread->fresh()->title)->toBe('Långlivad');

    $this->actingAs($thread->user)
        ->get(route('home'))
        ->assertSee('Långlivad');

    $this->actingAs($thread->user)
        ->patch(route('threads.update', $thread), ['title' => '   '])
        ->assertRedirect(route('threads.show', $thread));

    expect($thread->fresh()->title)->toBeNull();
});

it('stops another user from naming or archiving a thread', function () {
    $thread = Thread::factory()->create();
    $other = User::factory()->create();

    $this->actingAs($other)
        ->patch(route('threads.update', $thread), ['title' => 'Nej'])
        ->assertForbidden();

    $this->actingAs($other)
        ->patch(route('threads.archive', $thread))
        ->assertForbidden();

    expect($thread->fresh()->title)->toBeNull()
        ->and($thread->fresh()->archived_at)->toBeNull();
});

it('lets the author archive and open a thread again', function () {
    $thread = Thread::factory()->create(['body' => 'En liten tråd']);

    $this->actingAs($thread->user)
        ->patch(route('threads.archive', $thread))
        ->assertRedirect(route('threads.show', $thread));

    expect($thread->fresh()->isArchived())->toBeTrue();

    $this->actingAs($thread->user)
        ->get(route('home'))
        ->assertDontSee('En liten tråd');

    $this->actingAs($thread->user)
        ->patch(route('threads.unarchive', $thread))
        ->assertRedirect(route('threads.show', $thread));

    expect($thread->fresh()->isArchived())->toBeFalse();

    $this->actingAs($thread->user)
        ->get(route('home'))
        ->assertSee('En liten tråd');
});

it('lets an admin archive someone elses thread', function () {
    $thread = Thread::factory()->create();
    $admin = User::factory()->withRole(Role::Admin)->create();

    $this->actingAs($admin)
        ->patch(route('threads.archive', $thread))
        ->assertRedirect(route('threads.show', $thread));

    expect($thread->fresh()->isArchived())->toBeTrue();
});

it('lets the author delete a thread and keeps it out of normal queries', function () {
    $thread = Thread::factory()->create(['body' => 'Bort med den']);
    $reply = Reply::factory()->for($thread)->create(['body' => 'Ett svar']);

    $this->actingAs($thread->user)
        ->delete(route('threads.destroy', $thread))
        ->assertRedirect(route('home'));

    expect(Thread::query()->find($thread->id))->toBeNull()
        ->and(Thread::withTrashed()->find($thread->id))->not->toBeNull()
        ->and(Reply::query()->find($reply->id))->not->toBeNull();

    $this->actingAs($thread->user)
        ->get(route('threads.show', $thread))
        ->assertNotFound();

    $this->actingAs($thread->user)
        ->get(route('home'))
        ->assertDontSee('Bort med den');
});

it('restores a deleted thread without a reply that was deleted on its own', function () {
    $thread = Thread::factory()->create();
    $kept = Reply::factory()->for($thread)->create(['body' => 'Kvar']);
    $removed = Reply::factory()->for($thread)->create(['body' => 'Borta']);

    $removed->delete();
    $thread->delete();
    $thread->restore();

    expect($thread->fresh()->replies->pluck('body')->all())->toBe(['Kvar'])
        ->and(Reply::withTrashed()->find($kept->id))->not->toBeNull()
        ->and(Reply::withTrashed()->find($removed->id))->not->toBeNull();
});

it('stops another user from deleting a thread', function () {
    $thread = Thread::factory()->create();

    $this->actingAs(User::factory()->create())
        ->delete(route('threads.destroy', $thread))
        ->assertForbidden();

    expect($thread->fresh())->not->toBeNull();
});

it('lets the author delete one reply', function () {
    $thread = Thread::factory()->create(['body' => 'Start']);
    $reply = Reply::factory()->for($thread)->create(['body' => 'Ett svar']);

    $this->actingAs($reply->user)
        ->delete(route('replies.destroy', [$thread, $reply]))
        ->assertRedirect(route('threads.show', $thread));

    expect(Reply::query()->find($reply->id))->toBeNull()
        ->and($thread->fresh())->not->toBeNull();

    $this->actingAs($thread->user)
        ->get(route('threads.show', $thread))
        ->assertSuccessful()
        ->assertSee('Start')
        ->assertDontSee('Ett svar');
});

it('does not delete a reply through another thread', function () {
    $thread = Thread::factory()->create();
    $other = Thread::factory()->create();
    $reply = Reply::factory()->for($thread)->create();

    $this->actingAs($reply->user)
        ->delete(route('replies.destroy', [$other, $reply]))
        ->assertNotFound();

    expect(Reply::query()->find($reply->id))->not->toBeNull();
});

it('keeps the thread when its author is removed', function () {
    $author = User::factory()->create(['name' => 'Ada']);
    $thread = Thread::factory()->for($author)->create(['body' => 'Kvar efteråt']);

    $author->delete();

    expect($thread->fresh()->user_id)->toBeNull();

    $this->actingAs(User::factory()->create())
        ->get(route('threads.show', $thread))
        ->assertSuccessful()
        ->assertSee('Kvar efteråt')
        ->assertSee(__('threads.removed_user'));
});
