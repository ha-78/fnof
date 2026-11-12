@props([
    'current',
    'variant' => 'top',
])

@auth
    <nav
        aria-label="{{ __('layout.sections') }}"
        @class([
            'hidden items-center gap-2 sm:flex' => $variant === 'top',
            'fixed inset-x-0 bottom-0 z-20 border-t border-line bg-canvas pb-[env(safe-area-inset-bottom)] sm:hidden' => $variant === 'tabs',
        ])
    >
        <div @class([
            'contents' => $variant === 'top',
            'mx-auto flex h-16 max-w-5xl' => $variant === 'tabs',
        ])>
            @foreach (\App\Enums\Section::header() as $section)
                <a
                    href="{{ route($section->routeName()) }}"
                    @if ($variant === 'top' && $section !== $current) data-color="{{ $section->color() }}" @endif
                    @class([
                        'grid size-9 place-items-center rounded-full' => $variant === 'top',
                        'flex min-w-0 flex-1 flex-col items-center gap-0.5 px-1 pt-2' => $variant === 'tabs',
                        'bg-white text-field-ink shadow-sm' => $variant === 'top' && $section === $current,
                        'bg-tonal text-ink' => $variant === 'top' && $section !== $current,
                    ])
                    @if ($variant === 'top') aria-label="{{ $section->label() }}" @endif
                    @if ($section === $current) aria-current="page" @endif
                >
                    @if ($variant === 'tabs')
                        <span
                            @if ($section !== $current) data-color="{{ $section->color() }}" @endif
                            @class([
                                'grid size-9 place-items-center rounded-full',
                                'bg-white text-field-ink shadow-sm' => $section === $current,
                                'bg-tonal text-ink' => $section !== $current,
                            ])
                        >
                            <x-section-icon :section="$section" size="5" />
                        </span>
                        <span @class(['max-w-full truncate text-[11px] font-semibold', 'text-muted' => $section !== $current])>{{ $section->label() }}</span>
                    @else
                        <x-section-icon :section="$section" size="5" />
                    @endif
                </a>
            @endforeach
        </div>
    </nav>
@endauth
