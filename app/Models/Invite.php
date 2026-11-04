<?php

namespace App\Models;

use App\Enums\Role;
use Database\Factories\InviteFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'context_id',
    'inviter_id',
    'name',
    'email',
    'role',
    'token_hash',
    'token',
    'expires_at',
])]
#[Hidden(['token', 'token_hash'])]
class Invite extends Model
{
    /** @use HasFactory<InviteFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<Context, $this>
     */
    public function context(): BelongsTo
    {
        return $this->belongsTo(Context::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inviter_id');
    }

    public function link(): string
    {
        return route('invites.accept', ['token' => $this->token]);
    }

    /**
     * @param  Builder<Invite>  $query
     * @return Builder<Invite>
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where($query->qualifyColumn('expires_at'), '>', now());
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'role' => Role::class,
            'token' => 'encrypted',
            'expires_at' => 'datetime',
        ];
    }
}
