<?php

namespace Database\Factories;

use App\Models\Context;
use App\Models\ItemList;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ItemList>
 */
class ItemListFactory extends Factory
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
            'is_public' => true,
        ];
    }

    public function private(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_public' => false,
        ]);
    }
}
