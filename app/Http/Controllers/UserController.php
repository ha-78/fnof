<?php

namespace App\Http\Controllers;

use App\Enums\Feature;
use App\Enums\Role;
use App\Http\Requests\UpdateUserFeaturesRequest;
use App\Http\Requests\UpdateUserRoleRequest;
use App\Models\User;
use App\Services\DeviceRememberService;
use App\Services\UserAccessService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class UserController extends Controller
{
    public function account(Request $request, DeviceRememberService $devices): View
    {
        $user = $request->user();
        $current = $devices->findDeviceFromCookie($request);

        return view('users.account', [
            'user' => $user,
            'devices' => $user->rememberDevices()
                ->where('expires_at', '>', now())
                ->orderByDesc('last_used_at')
                ->get(['id', 'device_name', 'last_used_at']),
            'currentDeviceId' => $current !== null && $current->user_id === $user->id
                ? $current->id
                : null,
        ]);
    }

    public function index(): View
    {
        Gate::authorize('viewAny', User::class);

        return view('users.index', [
            'users' => User::query()->latest('id')->paginate(20),
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
