<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteBookmarkRequest;
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

        [$shared, $private] = $bookmarks->list($request->user())
            ->partition(fn (Bookmark $bookmark): bool => $bookmark->is_public);

        return view('bookmarks.index', [
            'shared' => $shared,
            'private' => $private,
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

    public function destroy(DeleteBookmarkRequest $request, Bookmark $bookmark, BookmarkService $bookmarks): RedirectResponse
    {
        $bookmarks->delete($request->user(), $bookmark);

        return to_route('bookmarks.index')->with('status', __('bookmarks.deleted'));
    }
}
