@props(['current'])

@auth
    <nav class="flex flex-wrap items-center justify-end gap-2 text-sm">
        @foreach (\App\Enums\Section::header() as $section)
            <a
                href="{{ route($section->routeName()) }}"
                class="inline-flex rounded-full p-1.5 text-[var(--color-d-5)]"
                style="background-color: {{ $section === $current ? 'var(--color-d-1)' : 'var(--color-'.$section->color().'-2)' }}"
                aria-label="{{ $section->label() }}"
                @if ($section === $current) aria-current="page" @endif
            >
                <x-section-icon :section="$section" />
            </a>
        @endforeach
    </nav>
@endauth
