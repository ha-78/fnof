<?php

namespace App\Policies;

use App\Models\Bookmark;
use App\Models\User;
use App\Policies\Concerns\ManagesOwnedRecord;

class BookmarkPolicy
{
    use ManagesOwnedRecord;

    public function viewAny(User $actor): bool
    {
        return true;
    }

    public function view(User $actor, Bookmark $bookmark): bool
    {
        if ((int) $bookmark->context_id !== $actor->currentContextId()) {
            return false;
        }

        if ($bookmark->is_public) {
            return true;
        }

        return (int) $actor->getKey() === (int) $bookmark->user_id;
    }

    public function create(User $actor): bool
    {
        return true;
    }

    public function update(User $actor, Bookmark $bookmark): bool
    {
        return $this->managesOwnedRecord($actor, $bookmark->user_id)
            && (int) $bookmark->context_id === $actor->currentContextId();
    }

    public function delete(User $actor, Bookmark $bookmark): bool
    {
        return (int) $actor->getKey() === (int) $bookmark->user_id
            && (int) $bookmark->context_id === $actor->currentContextId();
    }
}
