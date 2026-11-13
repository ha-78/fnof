@props(['list'])

{{-- One list on the edit lists page. --}}
<div {{ $attributes->class('flex min-h-13 items-center gap-1 py-1.5 pr-1.5 pl-4') }}>
    <div class="min-w-0 flex-1 py-1">
        <p class="truncate text-[15px] font-semibold text-ink">{{ $list->name }}</p>
        <p class="truncate text-[13px] text-muted">
            {{ $list->user->name }}
            <span aria-hidden="true"> · </span>
            <time datetime="{{ $list->created_at->timezone(config('app.timezone'))->toDateString() }}">{{ $list->created_at->timezone(config('app.timezone'))->format('Y-m-d') }}</time>
        </p>
    </div>

    @can('delete', $list)
        <form method="POST" action="{{ route('lists.destroy', $list) }}" onsubmit="return confirm(@js(__('lists.confirm_delete')))">
            @csrf
            @method('DELETE')

            <x-ui.button type="submit" variant="ghost" icon :aria-label="__('lists.delete')">
                <span class="text-muted"><x-icon name="trash" size="5" /></span>
            </x-ui.button>
        </form>
    @endcan
</div>
