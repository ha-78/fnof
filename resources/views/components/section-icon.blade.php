@props(['section'])

@if ($section === \App\Enums\Section::Start)
    <x-icon name="home" />
@elseif ($section === \App\Enums\Section::Threads)
    <x-icon name="chat" />
@elseif ($section === \App\Enums\Section::Lists)
    <x-icon name="list" />
@elseif ($section === \App\Enums\Section::Bookmarks)
    <x-icon name="bookmark" />
@elseif ($section === \App\Enums\Section::Meals)
    <x-icon name="utensils" />
@else
    <x-icon name="user" />
@endif
