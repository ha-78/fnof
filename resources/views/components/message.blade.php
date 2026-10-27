@props(['body', 'author' => null, 'authorId' => null, 'postedAt', 'compact' => false, 'relativeDate' => false])

@php
    $writtenByCurrentUser = auth()->id() !== null && (int) $authorId === (int) auth()->id();
@endphp

<article {{ $attributes->class([
    'text-ink',
    'bg-message-mine' => $writtenByCurrentUser,
    'bg-message-other' => ! $writtenByCurrentUser,
    'rounded-3xl p-4' => ! $compact,
    'rounded-2xl px-3 py-2' => $compact,
]) }}>
    @if ($slot->isNotEmpty())
        {{ $slot }}
    @endif

    <p @class(['text-muted', 'text-sm' => ! $compact, 'text-xs' => $compact])>
        {{ $author ?? __('threads.removed_user') }}
        <span aria-hidden="true"> · </span>
        <time datetime="{{ $postedAt->toAtomString() }}" @if ($relativeDate) data-relative @endif>{{ $relativeDate ? \App\Support\RelativeDate::label($postedAt) : $postedAt->timezone(config('app.timezone'))->format('Y-m-d H:i') }}</time>
    </p>
    <div @class(['whitespace-pre-wrap', 'mt-2' => ! $compact, 'mt-1 text-sm' => $compact])>{{ $body }}</div>
</article>
