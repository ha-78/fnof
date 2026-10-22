<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserRememberDevice;
use App\Support\ColorTheme;
use App\Support\DeviceNameParser;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Cookie as SymfonyCookie;

class DeviceRememberService
{
    public function cookieName(): string
    {
        return (string) config('device_remember.cookie_name');
    }

    public function lifetimeDays(): int
    {
        return max(1, (int) config('device_remember.lifetime_days'));
    }

    public function issue(User $user, Request $request): UserRememberDevice
    {
        $existing = $this->findDeviceFromCookie($request);
        $parsed = $this->parseCookieValue($request->cookie($this->cookieName()));

        if ($existing !== null && $existing->user_id === $user->id && $parsed !== null) {
            return $this->extend($existing, $request, $parsed['plain_token']);
        }

        if ($existing !== null) {
            $existing->delete();
        }

        $plainToken = Str::random(64);
        $expiresAt = now()->addDays($this->lifetimeDays());

        $device = $user->rememberDevices()->create([
            'token' => $this->hashToken($plainToken),
            'device_name' => DeviceNameParser::fromUserAgent($request->userAgent()),
            'user_agent' => $request->userAgent(),
            'ip_address' => $request->ip(),
            'color_theme' => $this->themeFromRequest($request),
            'last_used_at' => now(),
            'expires_at' => $expiresAt,
        ]);

        $this->queueCookie($device->id, $plainToken, $expiresAt);

        return $device;
    }

    public function authenticate(Request $request): ?User
    {
        $device = $this->findDeviceFromCookie($request);

        if ($device === null) {
            return null;
        }

        $parsed = $this->parseCookieValue($request->cookie($this->cookieName()));

        if ($parsed !== null) {
            $this->extend($device, $request, $parsed['plain_token']);
        }

        return $device->user;
    }

    public function findDeviceFromCookie(Request $request): ?UserRememberDevice
    {
        $parsed = $this->parseCookieValue($request->cookie($this->cookieName()));

        if ($parsed === null) {
            return null;
        }

        $device = UserRememberDevice::query()
            ->whereKey($parsed['id'])
            ->where('expires_at', '>', now())
            ->first();

        if ($device === null) {
            return null;
        }

        if (! hash_equals($device->token, $this->hashToken($parsed['plain_token']))) {
            return null;
        }

        return $device;
    }

    public function extend(UserRememberDevice $device, Request $request, string $plainToken): UserRememberDevice
    {
        $expiresAt = now()->addDays($this->lifetimeDays());

        $attributes = [
            'last_used_at' => now(),
            'expires_at' => $expiresAt,
            'ip_address' => $request->ip(),
        ];

        if ($request->userAgent() !== null) {
            $attributes['user_agent'] = $request->userAgent();
            $attributes['device_name'] = DeviceNameParser::fromUserAgent($request->userAgent());
        }

        $theme = $this->optionalThemeFromRequest($request);

        if ($theme !== null) {
            $attributes['color_theme'] = $theme;
        }

        $device->update($attributes);
        $this->queueCookie($device->id, $plainToken, $expiresAt);

        return $device->refresh();
    }

    public function updateTheme(UserRememberDevice $device, string $theme): void
    {
        $device->update([
            'color_theme' => ColorTheme::normalize($theme),
        ]);
    }

    public function revokeFromRequest(Request $request): void
    {
        $device = $this->findDeviceFromCookie($request);

        if ($device !== null) {
            $device->delete();
        }

        $this->forgetCookie();
    }

    public function revokeAllForUser(User $user): void
    {
        $user->rememberDevices()->delete();
    }

    public function themeFromRequest(Request $request): string
    {
        return ColorTheme::normalize(
            $request->input('color_theme')
            ?? $request->header('X-Fnof-Theme'),
        );
    }

    public function optionalThemeFromRequest(Request $request): ?string
    {
        $raw = $request->input('color_theme') ?? $request->header('X-Fnof-Theme');

        if ($raw === null || $raw === '') {
            return null;
        }

        return ColorTheme::normalize($raw);
    }

    public function normalizedDeviceTheme(?UserRememberDevice $device): ?string
    {
        if ($device === null || $device->color_theme === null) {
            return null;
        }

        return ColorTheme::normalize($device->color_theme);
    }

    public function touchAuthenticatedDevice(Request $request): ?UserRememberDevice
    {
        if (! Auth::check()) {
            return null;
        }

        $device = $this->findDeviceFromCookie($request);

        if ($device === null || $device->user_id !== Auth::id()) {
            return null;
        }

        $parsed = $this->parseCookieValue($request->cookie($this->cookieName()));

        if ($parsed === null) {
            return null;
        }

        return $this->extend($device, $request, $parsed['plain_token']);
    }

    public function forgetCookie(): void
    {
        Cookie::queue(Cookie::forget($this->cookieName()));
    }

    /**
     * @return array{id: int, plain_token: string}|null
     */
    private function parseCookieValue(?string $value): ?array
    {
        if ($value === null || $value === '') {
            return null;
        }

        $parts = explode('|', $value, 2);

        if (count($parts) !== 2 || ! ctype_digit($parts[0]) || $parts[1] === '') {
            return null;
        }

        return [
            'id' => (int) $parts[0],
            'plain_token' => $parts[1],
        ];
    }

    private function hashToken(string $plainToken): string
    {
        return hash('sha256', $plainToken);
    }

    private function queueCookie(int $deviceId, string $plainToken, CarbonInterface $expiresAt): void
    {
        Cookie::queue($this->makeCookie($deviceId, $plainToken, $expiresAt));
    }

    private function makeCookie(int $deviceId, string $plainToken, CarbonInterface $expiresAt): SymfonyCookie
    {
        $minutes = max(1, (int) now()->diffInMinutes($expiresAt, true));

        return Cookie::make(
            $this->cookieName(),
            $deviceId.'|'.$plainToken,
            $minutes,
            '/',
            null,
            (bool) config('session.secure', false),
            true,
            false,
            config('session.same_site', 'lax'),
        );
    }
}
