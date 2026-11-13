@props(['bookmark'])

<div {{ $attributes->class('flex min-h-13 items-center gap-1 py-1.5 pr-1.5 pl-4') }}>
    <a href="{{ $bookmark->url }}" target="_blank" rel="noopener noreferrer" class="min-w-0 flex-1 py-1 text-ink">
        <span class="block truncate text-[15px] font-semibold">{{ $bookmark->name }}</span>
        <span class="block truncate text-[13px] text-muted">{{ $bookmark->url }}</span>
        <span class="block truncate text-[13px] text-muted">
            {{ $bookmark->user->name }}
            <span aria-hidden="true"> · </span>
            <time datetime="{{ $bookmark->created_at->timezone(config('app.timezone'))->toDateString() }}">{{ $bookmark->created_at->timezone(config('app.timezone'))->format('Y-m-d') }}</time>
        </span>
    </a>

    @can('delete', $bookmark)
        <form method="POST" action="{{ route('bookmarks.destroy', $bookmark) }}" onsubmit="return confirm(@js(__('bookmarks.confirm_delete')))">
            @csrf
            @method('DELETE')

            <x-ui.button type="submit" variant="ghost" icon :aria-label="__('bookmarks.delete')">
                <span class="text-muted"><x-icon name="trash" size="5" /></span>
            </x-ui.button>
        </form>
    @endcan
</div>
