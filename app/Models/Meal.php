<?php

namespace App\Models;

use App\Enums\MealStatus;
use Database\Factories\MealFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['context_id', 'user_id', 'name', 'recipe_link', 'comment', 'status'])]
class Meal extends Model
{
    /** @use HasFactory<MealFactory> */
    use HasFactory;

    protected $attributes = [
        'status' => 'wishlist',
    ];

    public function hasDetails(): bool
    {
        return filled($this->recipe_link) || filled($this->comment);
    }

    /**
     * Meals in the user's household.
     *
     * @param  Builder<Meal>  $query
     * @return Builder<Meal>
     */
    public function scopeForContext(Builder $query, User $user): Builder
    {
        return $query->where($query->qualifyColumn('context_id'), $user->currentContextId());
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
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => MealStatus::class,
        ];
    }
}
