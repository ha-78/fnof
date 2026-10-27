@props(['date'])

<article {{ $attributes->class('rounded-2xl bg-calendar px-2 py-3 text-center text-calendar-ink') }}>
    <time datetime="{{ $date->datetime }}" class="block">
        <span class="block">{{ $date->weekday }}</span>
        <span class="mt-1 block text-5xl font-bold leading-none tabular-nums">{{ $date->day }}</span>
        <span class="mt-2 block">{{ $date->month }}</span>
    </time>
</article>
