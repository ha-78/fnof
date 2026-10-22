<?php

namespace App\Http\Controllers;

use App\Services\DeviceRememberService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DeviceRememberController extends Controller
{
    public function updateTheme(Request $request, DeviceRememberService $deviceRememberService): Response
    {
        $request->validate([
            'theme' => ['required', 'string', 'max:32'],
        ]);

        $device = $deviceRememberService->findDeviceFromCookie($request);

        if ($device === null || $device->user_id !== $request->user()->id) {
            return response()->noContent();
        }

        $deviceRememberService->updateTheme($device, $request->string('theme')->toString());

        return response()->noContent();
    }
}
