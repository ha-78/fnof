<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name'])]
class Context extends Model
{
    /**
     * @return HasMany<ContextUser, $this>
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(ContextUser::class);
    }
}
