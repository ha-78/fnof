<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMealRequest;
use App\Models\Meal;
use App\Services\MealService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class MealsController extends Controller
{
    public function index(Request $request, MealService $meals): View
    {
        Gate::authorize('viewAny', Meal::class);

        return view('meals.index', [
            'meals' => $meals->wishlist($request->user()),
        ]);
    }

    public function store(StoreMealRequest $request, MealService $meals): RedirectResponse
    {
        $meals->create(
            $request->user(),
            $request->validated('name'),
            $request->validated('recipe_link'),
            $request->validated('comment'),
        );

        return to_route('meals.index')->with('status', __('meals.created'));
    }
}
