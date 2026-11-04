<?php

namespace App\Services;

use App\Models\Bookmark;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;

class BookmarkService
{
    /**
     * @return Collection<int, Bookmark>
     */
    public function list(User $user): Collection
    {
        return Bookmark::query()
            ->with('user')
            ->visibleTo($user)
            ->orderBy('name')
            ->orderBy('id')
            ->get();
    }

    public function create(User $owner, string $name, string $url, bool $isPublic): Bookmark
    {
        if (! $owner->can('create', Bookmark::class)) {
            throw new AuthorizationException;
        }

        return $owner->bookmarks()->create([
            'context_id' => $owner->currentContextId(),
            'name' => $name,
            'url' => $url,
            'is_public' => $isPublic,
        ]);
    }
}
