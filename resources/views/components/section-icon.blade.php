@props(['section', 'size' => '6'])

@if ($section === \App\Enums\Section::Start)
    <x-icon name="home" :size="$size" />
@elseif ($section === \App\Enums\Section::Threads)
    <x-icon name="chat" :size="$size" />
@elseif ($section === \App\Enums\Section::Lists)
    <x-icon name="list" :size="$size" />
@elseif ($section === \App\Enums\Section::Bookmarks)
    <x-icon name="bookmark" :size="$size" />
@elseif ($section === \App\Enums\Section::Meals)
    <x-icon name="utensils" :size="$size" />
@else
    <x-icon name="user" :size="$size" />
@endif
