<?php

namespace App\Support;

class ColorTheme
{
    public const DEFAULT = 'default';

    /** @var list<string> */
    private const VALID = ['default', 'light', 'dark', 'colorful'];

    public static function normalize(?string $theme): string
    {
        if ($theme !== null && in_array($theme, self::VALID, true)) {
            return $theme;
        }

        return self::DEFAULT;
    }
}
