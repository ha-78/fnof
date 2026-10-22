<?php

namespace App\Policies;

use App\Models\Thread;
use App\Models\User;
use App\Policies\Concerns\ManagesOwnedRecord;

class ThreadPolicy
{
    use ManagesOwnedRecord;

    public function viewAny(User $actor): bool
    {
        return true;
    }

    public function view(User $actor, Thread $thread): bool
    {
        return true;
    }

    public function create(User $actor): bool
    {
        return true;
    }

    public function reply(User $actor, Thread $thread): bool
    {
        return ! $thread->isArchived();
    }

    public function update(User $actor, Thread $thread): bool
    {
        return $this->managesOwnedRecord($actor, $thread->user_id);
    }

    public function archive(User $actor, Thread $thread): bool
    {
        return $this->managesOwnedRecord($actor, $thread->user_id);
    }

    public function delete(User $actor, Thread $thread): bool
    {
        return $this->managesOwnedRecord($actor, $thread->user_id);
    }
}
