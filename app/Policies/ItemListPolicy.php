<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\ItemList;
use App\Models\User;
use App\Policies\Concerns\ManagesOwnedRecord;

class ItemListPolicy
{
    use ManagesOwnedRecord;

    public function viewAny(User $actor): bool
    {
        return true;
    }

    public function view(User $actor, ItemList $list): bool
    {
        if ((int) $list->context_id !== $actor->currentContextId()) {
            return false;
        }

        if ($list->is_public) {
            return true;
        }

        return (int) $actor->getKey() === (int) $list->user_id;
    }

    public function manage(User $actor): bool
    {
        return $actor->role->atLeast(Role::Admin);
    }

    public function create(User $actor): bool
    {
        return $this->manage($actor);
    }

    public function update(User $actor, ItemList $list): bool
    {
        return $this->managesOwnedRecord($actor, $list->user_id)
            && (int) $list->context_id === $actor->currentContextId();
    }

    public function delete(User $actor, ItemList $list): bool
    {
        return $this->manage($actor)
            && (int) $list->context_id === $actor->currentContextId();
    }
}
