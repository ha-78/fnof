<?php

use App\Support\RelativeDate;
use Tests\TestCase;

uses(TestCase::class);

it('says today, yesterday, or how many days ago', function () {
    $locale = app()->getLocale();
    $this->travelTo('2026-10-27 15:00:00');

    try {
        app()->setLocale('sv');

        expect(RelativeDate::label(now()))->toBe('idag')
            ->and(RelativeDate::label(now()->subHours(4)))->toBe('idag')
            ->and(RelativeDate::label(now()->subDay()))->toBe('igår')
            ->and(RelativeDate::label(now()->subDays(5)))->toBe('5 dagar sedan');

        app()->setLocale('en');

        expect(RelativeDate::label(now()))->toBe('today')
            ->and(RelativeDate::label(now()->subDay()))->toBe('yesterday')
            ->and(RelativeDate::label(now()->subDays(5)))->toBe('5 days ago');
    } finally {
        app()->setLocale($locale);
    }
});

it('counts the previous calendar day as yesterday across midnight', function () {
    $locale = app()->getLocale();
    $this->travelTo('2026-10-27 00:10:00');

    try {
        app()->setLocale('sv');

        expect(RelativeDate::label(now()->subHour()))->toBe('igår');
    } finally {
        app()->setLocale($locale);
    }
});
