<?php

namespace App\Http\Middleware;

use App\Services\DeviceRememberService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateDeviceRemember
{
    public function __construct(
        private DeviceRememberService $deviceRememberService,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            $user = $this->deviceRememberService->authenticate($request);

            if ($user !== null) {
                Auth::login($user);
                $request->session()->regenerate();
            }
        } else {
            $this->deviceRememberService->touchAuthenticatedDevice($request);
        }

        $device = $this->deviceRememberService->findDeviceFromCookie($request);
        $theme = $this->deviceRememberService->normalizedDeviceTheme($device);

        if ($theme !== null) {
            $request->attributes->set('device_color_theme', $theme);
            view()->share('deviceColorTheme', $theme);
        }

        return $next($request);
    }
}
