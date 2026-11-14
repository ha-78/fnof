@props(['body', 'author' => null, 'authorId' => null, 'postedAt', 'relativeDate' => false])

{{-- A speech bubble: your own on the right in step 2, others on the left on the surface. Extra slot content (such as a delete button) sits beside it. --}}
@php($mine = auth()->id() !== null && (int) $authorId === (int) auth()->id())

<div {{ $attributes->class(['flex items-end gap-2', 'flex-row-reverse' => $mine]) }}>
    @unless ($mine)
        <span aria-hidden="true" class="grid size-8 shrink-0 place-items-center rounded-full bg-tonal text-[13px] font-semibold text-tonal-ink">{{ Str::upper(Str::substr($author ?? '?', 0, 1)) }}</span>
    @endunless

    <article @class([
        'max-w-[80%] min-w-0 rounded-[16px] px-3.5 py-2.5',
        'rounded-br-[4px] bg-bubble-mine text-bubble-mine-ink' => $mine,
        'rounded-bl-[4px] border border-surface-line bg-surface text-ink' => ! $mine,
    ])>
        <p class="text-[12px] font-semibold">
            <x-author-name :author-id="$authorId" :name="$author" />
            <span aria-hidden="true"> · </span>
            <time class="font-normal" datetime="{{ $postedAt->toAtomString() }}" data-posted @if ($relativeDate) data-relative @endif>{{ $relativeDate ? \App\Support\RelativeDate::label($postedAt) : $postedAt->timezone(config('app.timezone'))->format('Y-m-d H:i') }}</time>
        </p>
        <div class="mt-0.5 text-[15px] leading-snug break-words whitespace-pre-wrap">{{ $body }}</div>
    </article>

    {{ $slot }}
</div>
