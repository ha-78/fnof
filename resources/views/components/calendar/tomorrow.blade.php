@props(['date'])

<article {{ $attributes->class('w-full rounded-3xl bg-calendar p-5 text-center text-calendar-ink') }}>
    <p class="text-sm font-medium">{{ __('calendar.tomorrow') }} - {{ $date->weekday }}</p>
    <time datetime="{{ $date->datetime }}" class="mt-3 block">
        <span class="block text-5xl font-bold leading-none tabular-nums">{{ $date->day }}</span>
        <span class="mt-2 block">{{ $date->month }}</span>
    </time>
</article>
