@props(['items'])

<div {{ $attributes->class('lists-surface overflow-x-auto rounded-3xl') }}>
    <table class="lists-table w-full text-left text-sm">
        <thead>
            <tr>
                <th class="px-4 py-3 text-center"><span class="sr-only">{{ __('lists.public') }}</span></th>
                <th class="px-4 py-3 font-medium text-ink">{{ __('lists.name') }}</th>
                <th class="px-4 py-3 font-medium text-ink">{{ __('lists.description') }}</th>
                @if ($items->contains(fn ($row) => $row->estimate !== null))
                    <th class="px-4 py-3 font-medium text-ink">{{ __('lists.estimate') }}</th>
                @endif
                <th class="px-4 py-3 font-medium text-ink">{{ __('lists.handled_by') }}</th>
                <th class="px-4 py-3 font-medium text-ink">{{ __('lists.handled_at') }}</th>
            </tr>
        </thead>
        @if ($items->isNotEmpty())
            <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td class="px-4 py-3 text-center">
                            @if ($item->is_public)
                                <span class="inline-flex text-ink">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
                                    </svg>
                                    <span class="sr-only">{{ __('lists.public') }}</span>
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-medium">{{ $item->name }}</td>
                        <td class="px-4 py-3 whitespace-pre-wrap break-words">{{ $item->description }}</td>
                        @if ($items->contains(fn ($row) => $row->estimate !== null))
                            <td class="px-4 py-3 whitespace-nowrap tabular-nums">{{ $item->estimate }}</td>
                        @endif
                        <td class="px-4 py-3 whitespace-nowrap">{{ $item->handler?->name }}</td>
                        <td class="px-4 py-3 whitespace-nowrap tabular-nums">
                            @if ($item->handled_at)
                                <time datetime="{{ $item->handled_at->timezone(config('app.timezone'))->toDateString() }}">{{ $item->handled_at->timezone(config('app.timezone'))->format('Y-m-d') }}</time>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        @endif
    </table>

    @if ($items->isEmpty())
        <p class="px-4 py-3 text-sm text-ink">{{ __('lists.items_empty') }}</p>
    @endif
</div>
