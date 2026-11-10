<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteListRequest;
use App\Http\Requests\StoreListRequest;
use App\Models\ItemList;
use App\Models\ListItem;
use App\Services\ListItemService;
use App\Services\ListService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ListController extends Controller
{
    public function index(Request $request, ListService $lists, ListItemService $items): View
    {
        Gate::authorize('viewAny', ItemList::class);
        Gate::authorize('viewAny', ListItem::class);

        return view('lists.index', [
            'lists' => $lists->forUser($request->user()),
            'items' => $items->forUser($request->user()),
        ]);
    }

    public function edit(Request $request, ListService $lists): View
    {
        Gate::authorize('create', ItemList::class);

        return view('lists.edit', [
            'lists' => $lists->forUser($request->user()),
        ]);
    }

    public function store(StoreListRequest $request, ListService $lists): RedirectResponse
    {
        $lists->create(
            $request->user(),
            $request->validated('name'),
            (bool) $request->validated('is_public'),
        );

        return to_route('lists.edit')->with('status', __('lists.created'));
    }

    public function destroy(DeleteListRequest $request, ItemList $list, ListService $lists): RedirectResponse
    {
        $lists->delete($request->user(), $list);

        return to_route('lists.edit')->with('status', __('lists.deleted'));
    }
}
