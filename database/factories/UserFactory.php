<?php

namespace Database\Factories;

use App\Enums\Feature;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (User $user): void {
            if ($user->memberships()->doesntExist()) {
                $user->role = Role::Member;
            }
        });
    }

    public function withRole(Role $role): static
    {
        return $this->afterCreating(function (User $user) use ($role): void {
            $user->role = $role;
        });
    }

    public function withFeatures(Feature ...$features): static
    {
        return $this->afterCreating(function (User $user) use ($features): void {
            $membership = $user->contextMembership();

            foreach ($features as $feature) {
                $membership->featureOverrides()->create([
                    'feature' => $feature,
                    'granted' => true,
                ]);
            }
        });
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
