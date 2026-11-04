<?php

namespace Database\Factories;

use App\Models\Bookmark;
use App\Models\Context;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bookmark>
 */
class BookmarkFactory extends Factory
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
            'url' => fake()->url(),
            'is_public' => false,
        ];
    }

    public function public(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_public' => true,
        ]);
    }
}
