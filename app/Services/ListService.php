<?php

namespace App\Services;

use App\Models\ItemList;
use App\Models\ListItem;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /**
     * @return Collection<int, ItemList>
     */
    public function forUserWithItems(User $user): Collection
    {
        $item = new ListItem;

        return $this->forUser($user)->load([
            'items' => function (HasMany $query) use ($user, $item): void {
                $query->visibleTo($user)
                    ->whereNull($item->qualifyColumn('handled_at'))
                    ->orderBy($item->qualifyColumn('name'))
                    ->orderBy($item->qualifyColumn('id'));
            },
        ]);
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
