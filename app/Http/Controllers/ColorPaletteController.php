<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ColorPaletteController extends Controller
{
    public function __invoke(): View
    {
        return view('colors.index', [
            'families' => [
                ['letter' => 'a', 'name' => __('colors.green'), 'used_for' => __('colors.threads'), 'token' => 'color-a'],
                ['letter' => 'b', 'name' => __('colors.burgundy'), 'used_for' => __('colors.calendar'), 'token' => 'color-b'],
                ['letter' => 'c', 'name' => __('colors.yellow'), 'used_for' => __('colors.lists'), 'token' => 'color-c'],
                ['letter' => 'd', 'name' => __('colors.brown'), 'used_for' => __('colors.start'), 'token' => 'color-d'],
                ['letter' => 'e', 'name' => __('colors.turquoise'), 'used_for' => __('colors.bookmarks'), 'token' => 'color-e'],
                ['letter' => 'f', 'name' => __('colors.purple'), 'used_for' => __('colors.meals'), 'token' => 'color-f'],
                ['letter' => 'g', 'name' => __('colors.blue'), 'used_for' => __('colors.notes'), 'token' => 'color-g'],
            ],
            'steps' => range(1, 5),
        ]);
    }
}
