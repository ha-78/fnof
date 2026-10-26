<?php

use App\Support\ColorTheme;
use App\Support\DeviceNameParser;
use Tests\TestCase;

uses(TestCase::class);

it('names a browser and device from the user agent', function () {
    $agent = 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1';

    expect(DeviceNameParser::fromUserAgent($agent))->toBe('Safari on iPhone')
        ->and(DeviceNameParser::fromUserAgent(null))->toBe(__('Unknown device'));
});

it('falls back to the default color theme', function () {
    expect(ColorTheme::normalize('colorful'))->toBe('light')
        ->and(ColorTheme::normalize('dark'))->toBe('dark')
        ->and(ColorTheme::normalize('nope'))->toBe('default')
        ->and(ColorTheme::normalize(null))->toBe('default');
});
