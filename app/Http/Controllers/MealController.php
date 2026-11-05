<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class MealController extends Controller
{
    public function __invoke(): View
    {
        return view('meals.index');
    }
}
