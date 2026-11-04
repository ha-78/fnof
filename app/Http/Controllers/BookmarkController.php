<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookmarkRequest;
use App\Models\Bookmark;
use App\Services\BookmarkService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class BookmarkController extends Controller
{
    public function index(Request $request, BookmarkService $bookmarks): View
    {
        Gate::authorize('viewAny', Bookmark::class);

        return view('bookmarks.index', [
            'bookmarks' => $bookmarks->list($request->user()),
        ]);
    }

    public function store(StoreBookmarkRequest $request, BookmarkService $bookmarks): RedirectResponse
    {
        $bookmarks->create(
            $request->user(),
            $request->validated('name'),
            $request->validated('url'),
            (bool) $request->validated('is_public'),
        );

        return to_route('bookmarks.index')->with('status', __('bookmarks.created'));
    }
}
