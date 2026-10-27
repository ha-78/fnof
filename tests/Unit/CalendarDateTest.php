<?php

use App\Models\CalendarDate;
use Tests\TestCase;

uses(TestCase::class);

it('formats a calendar day in the configured locale', function () {
    $locale = config('app.locale');
    $this->travelTo('2026-10-27 15:00:00');

    try {
        config(['app.locale' => 'sv']);

        $swedish = CalendarDate::from(now());

        expect($swedish->exists)->toBeFalse()
            ->and($swedish->datetime)->toBe('2026-10-27')
            ->and($swedish->day)->toBe('27')
            ->and($swedish->month)->toBe('oktober')
            ->and($swedish->weekday)->toBe('tisdag');

        config(['app.locale' => 'en']);

        $english = CalendarDate::from(now());

        expect($english->datetime)->toBe('2026-10-27')
            ->and($english->day)->toBe('27')
            ->and($english->month)->toBe('October')
            ->and($english->weekday)->toBe('Tuesday');
    } finally {
        config(['app.locale' => $locale]);
    }
});

it('builds today, tomorrow, and the eight days after', function () {
    $this->travelTo('2026-10-27 15:00:00');

    $calendar = CalendarDate::forHome();

    expect($calendar['today']->datetime)->toBe('2026-10-27')
        ->and($calendar['tomorrow']->datetime)->toBe('2026-10-28')
        ->and($calendar['days'])->toHaveCount(8)
        ->and($calendar['days']->first()->datetime)->toBe('2026-10-29')
        ->and($calendar['days']->last()->datetime)->toBe('2026-11-05');
});
