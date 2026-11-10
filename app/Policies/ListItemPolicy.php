<?php

namespace App\Policies;

use App\Enums\Feature;
use App\Models\ItemList;
use App\Models\ListItem;
use App\Models\User;

class ListItemPolicy
{
    public function viewAny(User $actor): bool
    {
        return true;
    }

    public function view(User $actor, ListItem $item): bool
    {
        if (! $actor->can('view', $item->list)) {
            return false;
        }

        if ($item->is_public) {
            return true;
        }

        return (int) $actor->getKey() === (int) $item->list->user_id;
    }

    public function create(User $actor, ?ItemList $list = null): bool
    {
        if (! $actor->canWrite()) {
            return false;
        }

        if ($list === null) {
            return true;
        }

        return $actor->can('view', $list);
    }

    public function publish(User $actor): bool
    {
        return $actor->canWrite() && $actor->allows(Feature::PublishListItems);
    }

    public function delete(User $actor, ListItem $item): bool
    {
        return $actor->canWrite()
            && (int) $actor->getKey() === (int) $item->list->user_id
            && (int) $item->list->context_id === $actor->currentContextId();
    }
}
