<?php

namespace Database\Factories;

use App\Enums\MealStatus;
use App\Models\Context;
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
            'context_id' => function (): int {
                $id = Context::query()->orderBy('id')->value('id');

                if ($id !== null) {
                    return (int) $id;
                }

                return Context::query()->create([
                    'name' => 'Familjen',
                ])->id;
            },
            'user_id' => User::factory(),
            'name' => fake()->words(3, true),
            'recipe_link' => fake()->optional()->url(),
            'comment' => fake()->sentence(),
            'status' => MealStatus::Wishlist,
        ];
    }
}
