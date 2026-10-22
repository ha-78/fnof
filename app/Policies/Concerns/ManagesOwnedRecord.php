<?php

namespace App\Policies\Concerns;

use App\Enums\Role;
use App\Models\User;

trait ManagesOwnedRecord
{
    private function managesOwnedRecord(User $actor, ?int $ownerId): bool
    {
        if ($actor->role->atLeast(Role::Admin)) {
            return true;
        }

        return $ownerId !== null && (int) $actor->getKey() === $ownerId;
    }
}
