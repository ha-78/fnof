<?php

namespace App\Models;

use Database\Factories\ItemListFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['context_id', 'user_id', 'name', 'is_public'])]
class ItemList extends Model
{
    /** @use HasFactory<ItemListFactory> */
    use HasFactory;

    protected $table = 'lists';

    protected $attributes = [
        'is_public' => true,
    ];

    /**
     * Lists the user owns in this context, plus public lists there.
     *
     * @param  Builder<ItemList>  $query
     * @return Builder<ItemList>
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        $contextId = $query->qualifyColumn('context_id');
        $userId = $query->qualifyColumn('user_id');
        $isPublic = $query->qualifyColumn('is_public');

        return $query
            ->where($contextId, $user->currentContextId())
            ->where(function (Builder $query) use ($user, $userId, $isPublic): void {
                $query->where($userId, $user->getKey())
                    ->orWhere($isPublic, true);
            });
    }

    /**
     * @return BelongsTo<Context, $this>
     */
    public function context(): BelongsTo
    {
        return $this->belongsTo(Context::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<ListItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(ListItem::class, 'list_id');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
        ];
    }
}
