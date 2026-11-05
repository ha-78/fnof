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

    /**
     * @return HasMany<Invite, $this>
     */
    public function invites(): HasMany
    {
        return $this->hasMany(Invite::class);
    }

    /**
     * @return HasMany<Bookmark, $this>
     */
    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    /**
     * @return HasMany<ItemList, $this>
     */
    public function lists(): HasMany
    {
        return $this->hasMany(ItemList::class);
    }
}
