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
                                    <x-icon name="globe" />
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
