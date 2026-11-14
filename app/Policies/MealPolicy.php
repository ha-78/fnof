<?php

namespace App\Policies;

use App\Enums\Feature;
use App\Models\User;

class MealPolicy
{
    public function viewAny(User $actor): bool
    {
        return true;
    }

    public function create(User $actor): bool
    {
        return $actor->canWrite() && $actor->allows(Feature::AddMealsToWishlist);
    }
}
