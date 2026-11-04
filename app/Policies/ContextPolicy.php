<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Context;
use App\Models\User;

class ContextPolicy
{
    public function update(User $actor, Context $context): bool
    {
        return $actor->role->atLeast(Role::Admin)
            && $actor->memberships()->whereBelongsTo($context)->exists();
    }
}
