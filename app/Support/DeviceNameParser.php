<?php

namespace App\Support;

use Illuminate\Support\Str;

class DeviceNameParser
{
    public static function fromUserAgent(?string $userAgent): string
    {
        if ($userAgent === null || $userAgent === '') {
            return __('Unknown device');
        }

        $os = self::detectOs($userAgent);
        $browser = self::detectBrowser($userAgent);

        if ($os !== null && $browser !== null) {
            return "{$browser} on {$os}";
        }

        if ($os !== null) {
            return $os;
        }

        if ($browser !== null) {
            return $browser;
        }

        return __('Unknown device');
    }

    private static function detectOs(string $userAgent): ?string
    {
        return match (true) {
            Str::contains($userAgent, 'iPhone') => 'iPhone',
            Str::contains($userAgent, 'iPad') => 'iPad',
            Str::contains($userAgent, 'Android') => 'Android',
            Str::contains($userAgent, ['Mac OS X', 'Macintosh']) => 'Mac',
            Str::contains($userAgent, 'Windows') => 'Windows',
            Str::contains($userAgent, 'Linux') => 'Linux',
            default => null,
        };
    }

    private static function detectBrowser(string $userAgent): ?string
    {
        return match (true) {
            Str::contains($userAgent, 'Edg/') => 'Edge',
            Str::contains($userAgent, 'Chrome/') && ! Str::contains($userAgent, 'Edg/') => 'Chrome',
            Str::contains($userAgent, 'Firefox/') => 'Firefox',
            Str::contains($userAgent, 'Safari/') && ! Str::contains($userAgent, 'Chrome/') => 'Safari',
            default => null,
        };
    }
}
