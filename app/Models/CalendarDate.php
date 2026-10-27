<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['datetime', 'day', 'month', 'weekday'])]
class CalendarDate extends Model
{
    public $timestamps = false;

    public static function from(CarbonInterface $moment): self
    {
        $date = $moment->copy()
            ->timezone(config('app.timezone'))
            ->locale(config('app.locale'));

        return new self([
            'datetime' => $date->toDateString(),
            'day' => $date->isoFormat('D'),
            'month' => $date->isoFormat('MMMM'),
            'weekday' => $date->isoFormat('dddd'),
        ]);
    }

    public static function forHome(): array
    {
        $today = Carbon::now()->timezone(config('app.timezone'));
        return [
            'today' => self::from($today),
            'tomorrow' => self::from($today->copy()->addDay()),
            'days' => collect(range(2, 9))->map(fn (int $offset): self => self::from($today->copy()->addDays($offset))),
        ];
    }
}
