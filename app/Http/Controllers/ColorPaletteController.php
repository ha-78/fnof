<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ColorPaletteController extends Controller
{
    public function __invoke(): View
    {
        return view('colors.index', [
            'families' => [
                __('colors.warm_green') => 'color-a',
                __('colors.burgundy') => 'color-b',
                __('colors.gold') => 'color-c',
                __('colors.brown') => 'color-d',
            ],
            'steps' => range(1, 5),
        ]);
    }
}
