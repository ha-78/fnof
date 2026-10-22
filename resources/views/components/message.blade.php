@props(['body', 'author' => null, 'postedAt', 'compact' => false])

<article {{ $attributes->class([
    'rounded-lg border border-stone-200 bg-white',
    'p-3' => ! $compact,
    'px-3 py-2' => $compact,
]) }}>
    <p @class(['text-stone-500', 'text-sm' => ! $compact, 'text-xs' => $compact])>
        {{ $author ?? __('threads.removed_user') }}
        <span aria-hidden="true"> · </span>
        <time datetime="{{ $postedAt->toAtomString() }}">{{ $postedAt->timezone(config('app.timezone'))->format('Y-m-d H:i') }}</time>
    </p>
    <div @class(['whitespace-pre-wrap', 'mt-2' => ! $compact, 'mt-1 text-sm' => $compact])>{{ $body }}</div>
</article>
