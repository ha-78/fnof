<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteInviteRequest;
use App\Http\Requests\StoreInviteRequest;
use App\Models\Context;
use App\Models\Invite;
use App\Models\User;
use App\Services\InviteService;
use Illuminate\Http\RedirectResponse;

class InviteController extends Controller
{
    public function store(StoreInviteRequest $request, InviteService $invites): RedirectResponse
    {
        $context = $request->context();
        $actor = $request->user();

        if (! $context instanceof Context || ! $actor instanceof User) {
            abort(403);
        }

        $invites->create($actor, $context, $request->validated());

        return to_route('users.index')->with('status', __('users.invites.created'));
    }

    public function destroy(DeleteInviteRequest $request, Invite $invite, InviteService $invites): RedirectResponse
    {
        $actor = $request->user();

        if (! $actor instanceof User) {
            abort(403);
        }

        $invites->delete($actor, $invite);

        return to_route('users.index')->with('status', __('users.invites.removed'));
    }
}
