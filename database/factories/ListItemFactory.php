<?php

namespace Database\Factories;

use App\Models\ItemList;
use App\Models\ListItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ListItem>
 */
class ListItemFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'list_id' => ItemList::factory(),
            'name' => fake()->words(3, true),
            'description' => null,
            'estimate' => null,
            'is_public' => true,
            'handled_at' => null,
            'handled_by' => null,
        ];
    }

    public function private(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_public' => false,
        ]);
    }
}
