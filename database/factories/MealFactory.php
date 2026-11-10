<?php

namespace Database\Factories;

use App\Enums\MealStatus;
use App\Models\Meal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Meal>
 */
class MealFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->words(3, true),
            'recipe_link' => fake()->optional()->url(),
            'comment' => fake()->sentence(),
            'status' => MealStatus::Wishlist,
        ];
    }
}
