<?php

namespace App\Services;

use App\Models\ItemList;
use App\Models\ListItem;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;

class ListItemService
{
    /**
     * @return Collection<int, ListItem>
     */
    public function forUser(User $user): Collection
    {
        $listName = ItemList::query()
            ->select((new ItemList)->qualifyColumn('name'))
            ->whereColumn(
                (new ItemList)->qualifyColumn('id'),
                (new ListItem)->qualifyColumn('list_id'),
            )
            ->limit(1);

        return ListItem::query()
            ->with('list')
            ->visibleTo($user)
            ->orderBy($listName)
            ->orderBy((new ListItem)->qualifyColumn('name'))
            ->orderBy((new ListItem)->qualifyColumn('id'))
            ->get();
    }

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

    public function delete(User $actor, ListItem $item): void
    {
        if (! $actor->can('delete', $item)) {
            throw new AuthorizationException;
        }

        $item->delete();
    }
}
