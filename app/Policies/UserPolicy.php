<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->role->atLeast(Role::Admin);
    }

    public function view(User $actor, User $subject): bool
    {
        return $actor->role->atLeast(Role::Admin);
    }

    public function assignRole(User $actor, User $subject, Role $role): bool
    {
        if ($actor->role === Role::God) {
            return true;
        }

        return $actor->role === Role::Admin
            && $subject->role === Role::Reader
            && $role === Role::User;
    }

    public function assignFeatures(User $actor, User $subject): bool
    {
        if (! $actor->role->atLeast(Role::Admin)) {
            return false;
        }

        return $subject->role === Role::User || $subject->role === Role::Reader;
    }
}
