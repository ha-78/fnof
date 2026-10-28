<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Feature;
use App\Enums\Role;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * @return HasMany<ContextUser, $this>
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(ContextUser::class);
    }

    /**
     * @return BelongsToMany<Context, $this>
     */
    public function contexts(): BelongsToMany
    {
        return $this->belongsToMany(Context::class)
            ->withPivot(['id', 'role'])
            ->withTimestamps();
    }

    /**
     * @return HasMany<UserFeature, $this>
     */
    public function featureOverrides(): HasMany
    {
        return $this->hasMany(UserFeature::class);
    }

    /**
     * @return HasMany<UserRememberDevice, $this>
     */
    public function rememberDevices(): HasMany
    {
        return $this->hasMany(UserRememberDevice::class);
    }

    /**
     * @return HasMany<Thread, $this>
     */
    public function threads(): HasMany
    {
        return $this->hasMany(Thread::class);
    }

    /**
     * @return HasMany<Reply, $this>
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Reply::class);
    }

    public function allows(Feature $feature): bool
    {
        if ($this->role->atLeast(Role::Admin)) {
            return true;
        }

        $override = $this->relationLoaded('featureOverrides')
            ? $this->featureOverrides->first(
                fn (UserFeature $row): bool => $row->feature === $feature,
            )
            : $this->featureOverrides()->where('feature', $feature)->first();

        return $override?->granted ?? $feature->defaultFor($this->role);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
