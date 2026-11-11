<?php

namespace App\Models;

use Database\Factories\ListItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['list_id', 'name', 'description', 'estimate', 'is_public', 'handled_at', 'handled_by'])]
class ListItem extends Model
{
    /** @use HasFactory<ListItemFactory> */
    use HasFactory;

    protected $attributes = [
        'is_public' => false,
    ];

    /**
     * Items on lists the user can see. Private items stay with the list owner.
     *
     * @param  Builder<ListItem>  $query
     * @return Builder<ListItem>
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        $listId = $query->qualifyColumn('list_id');
        $isPublic = $query->qualifyColumn('is_public');
        $visibleListIds = ItemList::query()->visibleTo($user)->select((new ItemList)->qualifyColumn('id'));
        $ownedListIds = ItemList::query()
            ->select((new ItemList)->qualifyColumn('id'))
            ->where((new ItemList)->qualifyColumn('context_id'), $user->currentContextId())
            ->where((new ItemList)->qualifyColumn('user_id'), $user->getKey());

        return $query
            ->whereIn($listId, $visibleListIds)
            ->where(function (Builder $query) use ($isPublic, $listId, $ownedListIds): void {
                $query->where($isPublic, true)
                    ->orWhereIn($listId, $ownedListIds);
            });
    }

    /**
     * @return BelongsTo<ItemList, $this>
     */
    public function itemList(): BelongsTo
    {
        return $this->belongsTo(ItemList::class, 'list_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'estimate' => 'integer',
            'is_public' => 'boolean',
            'handled_at' => 'datetime',
        ];
    }
}
