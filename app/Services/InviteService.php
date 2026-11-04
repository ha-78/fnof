<?php

namespace App\Services;

use App\Enums\Role;
use App\Models\Context;
use App\Models\Invite;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class InviteService
{
    public function canInvite(User $actor, ?Context $context): bool
    {
        return $context instanceof Context
            && $actor->can('create', [Invite::class, $context]);
    }

    /**
     * @return Collection<int, Invite>
     */
    public function pending(?Context $context): Collection
    {
        if (! $context instanceof Context) {
            return new Collection;
        }

        return $context->invites()->pending()->latest('id')->get();
    }

    public function pruneExpired(?Context $context): void
    {
        if (! $context instanceof Context) {
            return;
        }

        $context->invites()->where('expires_at', '<=', now())->delete();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(User $actor, Context $context, array $data): Invite
    {
        return DB::transaction(function () use ($actor, $context, $data): Invite {
            if (! $actor->can('create', [Invite::class, $context])) {
                throw new AuthorizationException;
            }

            $name = Str::of((string) ($data['name'] ?? ''))->squish()->toString();
            $email = Str::of((string) ($data['email'] ?? ''))->trim()->lower()->toString();

            Invite::query()
                ->where('email', $email)
                ->where('expires_at', '<=', now())
                ->delete();

            $pending = Invite::query()
                ->pending()
                ->where('email', $email)
                ->lockForUpdate()
                ->first();

            if ($pending !== null || User::query()->where('email', $email)->lockForUpdate()->exists()) {
                throw ValidationException::withMessages([
                    'email' => __('users.invites.email_taken'),
                ]);
            }

            $plainToken = Str::random(64);

            return $context->invites()->create([
                'inviter_id' => $actor->id,
                'name' => $name,
                'email' => $email,
                'role' => Role::from((string) ($data['role'] ?? '')),
                'token_hash' => $this->hashToken($plainToken),
                'token' => $plainToken,
                'expires_at' => $this->expiresAt(),
            ]);
        });
    }

    public function findPending(string $plainToken): ?Invite
    {
        if (Str::length($plainToken) !== 64) {
            return null;
        }

        return Invite::query()
            ->pending()
            ->where('token_hash', $this->hashToken($plainToken))
            ->first();
    }

    public function accept(string $plainToken, string $password): ?User
    {
        if (Str::length($plainToken) !== 64) {
            return null;
        }

        return DB::transaction(function () use ($plainToken, $password): ?User {
            $invite = Invite::query()
                ->pending()
                ->where('token_hash', $this->hashToken($plainToken))
                ->lockForUpdate()
                ->first();

            if ($invite === null) {
                return null;
            }

            if (User::query()->where('email', $invite->email)->lockForUpdate()->exists()) {
                throw ValidationException::withMessages([
                    'email' => __('users.invites.email_taken'),
                ]);
            }

            try {
                $user = new User([
                    'name' => $invite->name,
                    'email' => $invite->email,
                    'password' => $password,
                ]);
                $user->email_verified_at = now();
                $user->save();
            } catch (UniqueConstraintViolationException) {
                throw ValidationException::withMessages([
                    'email' => __('users.invites.email_taken'),
                ]);
            }

            $user->memberships()->create([
                'context_id' => $invite->context_id,
                'role' => $invite->role,
            ]);

            $invite->delete();

            return $user;
        });
    }

    public function delete(User $actor, Invite $invite): void
    {
        if (! $actor->can('delete', $invite)) {
            throw new AuthorizationException;
        }

        $invite->delete();
    }

    private function expiresAt(): CarbonInterface
    {
        return now()->addDays(max(1, (int) config('invites.lifetime_days')));
    }

    private function hashToken(string $plainToken): string
    {
        return hash('sha256', $plainToken);
    }
}
