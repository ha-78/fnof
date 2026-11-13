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
    public function index(Request $request, ListService $lists): View
    {
        Gate::authorize('viewAny', ItemList::class);
        Gate::authorize('viewAny', ListItem::class);

        $visible = $lists->forUserWithItems($request->user());
        $selected = $visible->firstWhere('id', $request->integer('list')) ?? $visible->first();

        return view('lists.index', [
            'lists' => $visible,
            'selected' => $selected?->id,
            'publicListIds' => $visible->where('is_public', true)->pluck('id')->values()->all(),
        ]);
    }

    public function show(Request $request, ItemList $list, ListItemService $items): View
    {
        Gate::authorize('view', $list);

        [$handled, $open] = $items->forList($request->user(), $list)
            ->partition(fn (ListItem $item): bool => $item->handled_at !== null);

        return view('lists.show', [
            'list' => $list,
            'open' => $open,
            'handled' => $handled,
        ]);
    }

    public function edit(Request $request, ListService $lists): View
    {
        Gate::authorize('create', ItemList::class);

        [$shared, $private] = $lists->forUser($request->user())
            ->partition(fn (ItemList $list): bool => $list->is_public);

        return view('lists.edit', [
            'shared' => $shared,
            'private' => $private,
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
