@props(['body', 'author' => null, 'postedAt', 'compact' => false, 'tone' => null, 'toneIndex' => 0])

@php
    $resolvedTone = $tone ?? ['sun', 'mint', 'lilac', 'rose'][$toneIndex % 4];

    $toneClass = match ($resolvedTone) {
        'mint' => 'bg-mint',
        'lilac' => 'bg-lilac',
        'rose' => 'bg-rose',
        'reply' => 'bg-reply',
        default => 'bg-sun',
    };
@endphp

<article {{ $attributes->class([
    'text-ink',
    $toneClass,
    'rounded-3xl p-4' => ! $compact,
    'rounded-2xl px-3 py-2' => $compact,
]) }}>
    <p @class(['text-muted', 'text-sm' => ! $compact, 'text-xs' => $compact])>
        {{ $author ?? __('threads.removed_user') }}
        <span aria-hidden="true"> · </span>
        <time datetime="{{ $postedAt->toAtomString() }}">{{ $postedAt->timezone(config('app.timezone'))->format('Y-m-d H:i') }}</time>
    </p>
    <div @class(['whitespace-pre-wrap', 'mt-2' => ! $compact, 'mt-1 text-sm' => $compact])>{{ $body }}</div>
</article>
