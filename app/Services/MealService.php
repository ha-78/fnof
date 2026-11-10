<?php

namespace App\Services;

use App\Enums\MealStatus;
use App\Models\Meal;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;

class MealService
{
    /**
     * @return Collection<int, Meal>
     */
    public function wishlist(): Collection
    {
        return Meal::query()
            ->with('user')
            ->where('status', MealStatus::Wishlist)
            ->orderByDesc('id')
            ->get();
    }

    public function create(User $owner, string $name, ?string $recipeLink, ?string $comment): Meal
    {
        if (! $owner->can('create', Meal::class)) {
            throw new AuthorizationException;
        }

        return $owner->meals()->create([
            'name' => $name,
            'recipe_link' => $recipeLink,
            'comment' => $comment,
            'status' => MealStatus::Wishlist,
        ]);
    }
}
