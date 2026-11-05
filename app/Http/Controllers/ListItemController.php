<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteListItemRequest;
use App\Http\Requests\StoreListItemRequest;
use App\Models\ListItem;
use App\Services\ListItemService;
use Illuminate\Http\RedirectResponse;

class ListItemController extends Controller
{
    public function store(StoreListItemRequest $request, ListItemService $items): RedirectResponse
    {
        $items->create(
            $request->user(),
            $request->list(),
            $request->validated('item_name'),
            $request->validated('item_description'),
            $request->estimate(),
            (bool) $request->validated('item_is_public'),
        );

        return to_route('lists.index')->with('status', __('lists.item_created'));
    }

    public function destroy(DeleteListItemRequest $request, ListItem $item, ListItemService $items): RedirectResponse
    {
        $items->delete($request->user(), $item);

        return to_route('lists.index')->with('status', __('lists.item_deleted'));
    }
}
