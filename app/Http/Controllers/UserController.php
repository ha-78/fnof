<?php

namespace App\Http\Controllers;

use App\Enums\Feature;
use App\Enums\Role;
use App\Http\Requests\UpdateUserFeaturesRequest;
use App\Http\Requests\UpdateUserRoleRequest;
use App\Models\User;
use App\Services\UserAccessService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('users.index', [
            'user' => $user,
            'users' => $user->can('viewAny', User::class)
                ? User::query()->latest('id')->paginate(20)
                : null,
        ]);
    }

    public function show(Request $request, User $user, UserAccessService $access): View
    {
        Gate::authorize('view', $user);

        $roles = $access->assignableRoles($request->user(), $user);

        return view('users.show', [
            'user' => $user->load('featureOverrides'),
            'assignableRoles' => $roles,
            'canChangeRole' => collect($roles)->contains(
                fn (Role $role): bool => $role !== $user->role,
            ),
            'canAssignFeatures' => $request->user()->can('assignFeatures', $user),
            'features' => Feature::cases(),
        ]);
    }

    public function updateRole(UpdateUserRoleRequest $request, User $user, UserAccessService $access): RedirectResponse
    {
        $access->changeRole($request->user(), $user, $request->enum('role', Role::class));

        return to_route('users.show', $user)->with('status', __('users.role_updated'));
    }

    public function updateFeatures(UpdateUserFeaturesRequest $request, User $user, UserAccessService $access): RedirectResponse
    {
        $access->syncFeatures($request->user(), $user, $request->grantedFeatures());

        return to_route('users.show', $user)->with('status', __('users.features_updated'));
    }
}
