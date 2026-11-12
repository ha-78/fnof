@props(['meal'])

<article {{ $attributes->class('meals-card rounded-3xl p-3 text-ink') }} @if ($meal->hasDetails()) x-data="{ open: false }" @endif>
    @if ($meal->hasDetails())
        <button type="button" class="flex w-full items-start justify-between gap-3 text-left" @click="open = ! open" :aria-expanded="open.toString()" aria-controls="meal-{{ $meal->id }}-details">
            <span class="whitespace-pre-wrap font-medium">{{ $meal->name }}</span>
            <x-icon name="chevron-down" size="5" class="mt-0.5 shrink-0 transition-transform" ::class="open ? 'rotate-180' : ''" />
        </button>

        <div id="meal-{{ $meal->id }}-details" x-show="open" x-cloak class="mt-3 space-y-2 text-sm">
            @if (filled($meal->recipe_link))
                <a href="{{ $meal->recipe_link }}" class="break-all hover:underline" target="_blank" rel="noopener noreferrer">{{ $meal->recipe_link }}</a>
            @endif

            @if (filled($meal->comment))
                <p class="whitespace-pre-wrap">{{ $meal->comment }}</p>
            @endif
        </div>
    @else
        <p class="whitespace-pre-wrap font-medium">{{ $meal->name }}</p>
    @endif

    <p class="mt-3 text-right text-sm italic text-muted">
        <time datetime="{{ $meal->created_at->timezone(config('app.timezone'))->toDateString() }}">{{ $meal->created_at->timezone(config('app.timezone'))->format('Y-m-d') }}</time>
        <span aria-hidden="true"> · </span>
        {{ $meal->user?->name }}
    </p>
</article>
