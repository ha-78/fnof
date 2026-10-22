<?php

namespace App\Services;

use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ThreadService
{
    public function list(bool $archived): LengthAwarePaginator
    {
        return Thread::query()
            ->with(['user', 'replies.user'])
            ->when(
                $archived,
                fn (Builder $query): Builder => $query->archived(),
                fn (Builder $query): Builder => $query->open(),
            )
            ->latest()
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();
    }

    public function open(User $author, string $body): Thread
    {
        if (! $author->can('create', Thread::class)) {
            throw new AuthorizationException;
        }

        return $author->threads()->create([
            'body' => $body,
        ]);
    }

    public function reply(User $author, Thread $thread, string $body): Reply
    {
        return DB::transaction(function () use ($author, $thread, $body): Reply {
            $locked = Thread::query()->lockForUpdate()->findOrFail($thread->getKey());

            if ($locked->isArchived()) {
                throw ValidationException::withMessages([
                    'body' => __('threads.archived_no_replies'),
                ]);
            }

            if (! $author->can('reply', $locked)) {
                throw new AuthorizationException;
            }

            return $locked->replies()->create([
                'user_id' => $author->getKey(),
                'body' => $body,
            ]);
        });
    }

    public function rename(User $actor, Thread $thread, ?string $title): void
    {
        if (! $actor->can('update', $thread)) {
            throw new AuthorizationException;
        }

        $thread->title = $title;
        $thread->save();
    }

    public function archive(User $actor, Thread $thread): void
    {
        if (! $actor->can('archive', $thread)) {
            throw new AuthorizationException;
        }

        $thread->archived_at = now();
        $thread->save();
    }

    public function unarchive(User $actor, Thread $thread): void
    {
        if (! $actor->can('archive', $thread)) {
            throw new AuthorizationException;
        }

        $thread->archived_at = null;
        $thread->save();
    }

    public function delete(User $actor, Thread $thread): void
    {
        if (! $actor->can('delete', $thread)) {
            throw new AuthorizationException;
        }

        $thread->delete();
    }

    public function deleteReply(User $actor, Reply $reply): void
    {
        if (! $actor->can('delete', $reply)) {
            throw new AuthorizationException;
        }

        $reply->delete();
    }
}
