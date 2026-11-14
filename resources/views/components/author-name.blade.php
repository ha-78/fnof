@props(['authorId' => null, 'name' => null])
{{-- "Me" for the signed-in user, the name otherwise, and a placeholder for a removed user. --}}
@if (auth()->id() !== null && (int) $authorId === (int) auth()->id()){{ __('threads.me') }}@else{{ $name ?? __('threads.removed_user') }}@endif
