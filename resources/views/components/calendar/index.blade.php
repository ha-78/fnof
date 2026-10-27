@props(['today', 'tomorrow', 'days'])

<div class="space-y-3">
    <x-calendar.today :date="$today" />
    <x-calendar.tomorrow :date="$tomorrow" />

    <div class="grid grid-cols-4 gap-3">
        @foreach ($days as $day)
            <x-calendar.day :date="$day" />
        @endforeach
    </div>
</div>
