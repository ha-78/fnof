<?php

namespace App\Http\Controllers;

use App\Http\Requests\AcceptInviteRequest;
use App\Services\InviteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AcceptInviteController extends Controller
{
    public function create(string $token, InviteService $invites): View
    {
        $invite = $invites->findPending($token);

        if ($invite === null) {
            abort(response()->view('invites.invalid', [], 404));
        }

        return view('invites.accept', [
            'invite' => $invite->load('context'),
        ]);
    }

    public function store(AcceptInviteRequest $request, string $token, InviteService $invites): RedirectResponse
    {
        $user = $invites->accept($token, (string) $request->validated('password'));

        if ($user === null) {
            abort(response()->view('invites.invalid', [], 404));
        }

        Auth::login($user);
        $request->session()->regenerate();

        return to_route('home');
    }
}
