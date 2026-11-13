<?php

namespace App\Services;

use App\Models\ItemList;
use App\Models\ListItem;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;

class ListItemService
{
    public function create(User $actor, ItemList $list, string $name, ?string $description, ?int $estimate, bool $isPublic): ListItem
    {
        if (! $actor->can('create', [ListItem::class, $list])) {
            throw new AuthorizationException;
        }

        return $list->items()->create([
            'name' => $name,
            'description' => $description,
            'estimate' => $estimate,
            'is_public' => $isPublic,
        ]);
    }

    /**
     * @return Collection<int, ListItem>
     */
    public function forList(User $user, ItemList $list): Collection
    {
        $item = new ListItem;

        return $list->items()
            ->with('handler')
            ->visibleTo($user)
            ->orderBy($item->qualifyColumn('handled_at'))
            ->orderBy($item->qualifyColumn('name'))
            ->orderBy($item->qualifyColumn('id'))
            ->get();
    }

    public function handle(User $actor, ListItem $item): void
    {
        if (! $actor->can('handle', $item)) {
            throw new AuthorizationException;
        }

        $item->update([
            'handled_at' => now(),
            'handled_by' => $actor->getKey(),
        ]);
    }

    public function unhandle(User $actor, ListItem $item): void
    {
        if (! $actor->can('unhandle', $item)) {
            throw new AuthorizationException;
        }

        $item->update([
            'handled_at' => null,
            'handled_by' => null,
        ]);
    }

    public function delete(User $actor, ListItem $item): void
    {
        if (! $actor->can('delete', $item)) {
            throw new AuthorizationException;
        }

        $item->delete();
    }
}
