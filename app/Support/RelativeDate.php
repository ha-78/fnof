<?php

namespace App\Support;

use Carbon\CarbonInterface;

class RelativeDate
{
    public static function label(CarbonInterface $moment): string
    {
        $zoned = $moment->copy()->timezone(config('app.timezone'));

        if ($zoned->isToday()) {
            return __('threads.today');
        }

        if ($zoned->isYesterday()) {
            return __('threads.yesterday');
        }

        $days = $zoned->copy()->startOfDay()->diff(now()->startOfDay())->days;

        return trans_choice('threads.days_ago', $days, ['count' => $days]);
    }
}
