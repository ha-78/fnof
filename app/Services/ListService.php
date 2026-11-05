<?php

namespace App\Services;

use App\Models\ItemList;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;

class ListService
{
    /**
     * @return Collection<int, ItemList>
     */
    public function forUser(User $user): Collection
    {
        return ItemList::query()
            ->with('user')
            ->visibleTo($user)
            ->orderBy('name')
            ->orderBy('id')
            ->get();
    }

    public function create(User $owner, string $name, bool $isPublic): ItemList
    {
        if (! $owner->can('create', ItemList::class)) {
            throw new AuthorizationException;
        }

        return $owner->lists()->create([
            'context_id' => $owner->currentContextId(),
            'name' => $name,
            'is_public' => $isPublic,
        ]);
    }

    public function delete(User $actor, ItemList $list): void
    {
        if (! $actor->can('delete', $list)) {
            throw new AuthorizationException;
        }

        $list->delete();
    }
}
