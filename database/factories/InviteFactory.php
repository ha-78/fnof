<?php

namespace Database\Factories;

use App\Enums\Role;
use App\Models\Context;
use App\Models\Invite;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Invite>
 */
class InviteFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $plainToken = Str::random(64);

        return [
            'context_id' => fn (): int => Context::query()->create([
                'name' => 'Familjen',
            ])->id,
            'inviter_id' => User::factory(),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'role' => Role::Reader,
            'token' => $plainToken,
            'token_hash' => hash('sha256', $plainToken),
            'expires_at' => now()->addDays(max(1, (int) config('invites.lifetime_days'))),
        ];
    }

    public function expired(): static
    {
        return $this->state(fn (): array => [
            'expires_at' => now()->subMinute(),
        ]);
    }
}
