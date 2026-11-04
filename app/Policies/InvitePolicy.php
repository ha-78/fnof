<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Context;
use App\Models\Invite;
use App\Models\User;

class InvitePolicy
{
    public function create(User $actor, Context $context): bool
    {
        return $this->manages($actor, $context->id);
    }

    public function delete(User $actor, Invite $invite): bool
    {
        return $this->manages($actor, $invite->context_id);
    }

    private function manages(User $actor, int $contextId): bool
    {
        if ($actor->role !== Role::God) {
            return false;
        }

        return $actor->memberships()->where('context_id', $contextId)->exists();
    }
}
