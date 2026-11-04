<?php

namespace Database\Factories;

use App\Models\Context;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Thread>
 */
class ThreadFactory extends Factory
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
            'body' => fake()->paragraph(),
            'title' => null,
        ];
    }

    public function archived(): static
    {
        return $this->state(fn (array $attributes): array => [
            'archived_at' => now(),
        ]);
    }
}
