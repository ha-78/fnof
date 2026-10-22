<?php

namespace App\Services;

use App\Enums\Feature;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UserAccessService
{
    public function changeRole(User $actor, User $subject, Role $role): void
    {
        DB::transaction(function () use ($actor, $subject, $role): void {
            $locked = User::query()->lockForUpdate()->findOrFail($subject->getKey());

            if (! $actor->can('assignRole', [$locked, $role])) {
                throw new AuthorizationException;
            }

            if ($locked->role === $role) {
                return;
            }

            if ($locked->role === Role::God && $role !== Role::God && ! $this->anotherGodExists($locked)) {
                throw ValidationException::withMessages([
                    'role' => __('users.last_god'),
                ]);
            }

            $locked->role = $role;
            $locked->save();
            $locked->featureOverrides()->delete();
        });
    }

    /**
     * @param  array<string, bool>  $granted
     */
    public function syncFeatures(User $actor, User $subject, array $granted): void
    {
        DB::transaction(function () use ($actor, $subject, $granted): void {
            $locked = User::query()->lockForUpdate()->findOrFail($subject->getKey());

            if (! $actor->can('assignFeatures', $locked)) {
                throw new AuthorizationException;
            }

            $locked->featureOverrides()->delete();

            foreach (Feature::cases() as $feature) {
                $isGranted = $granted[$feature->value] ?? false;

                if ($isGranted === $feature->defaultFor($locked->role)) {
                    continue;
                }

                $locked->featureOverrides()->create([
                    'feature' => $feature,
                    'granted' => $isGranted,
                ]);
            }
        });
    }

    /**
     * @return list<Role>
     */
    public function assignableRoles(User $actor, User $subject): array
    {
        return array_values(array_filter(
            Role::cases(),
            fn (Role $role): bool => $actor->can('assignRole', [$subject, $role]),
        ));
    }

    private function anotherGodExists(User $subject): bool
    {
        return User::query()
            ->where('role', Role::God)
            ->whereKeyNot($subject->getKey())
            ->orderBy('id')
            ->lockForUpdate()
            ->first() !== null;
    }
}
