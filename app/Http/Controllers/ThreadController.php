<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArchiveThreadRequest;
use App\Http\Requests\DeleteThreadRequest;
use App\Http\Requests\StoreThreadRequest;
use App\Http\Requests\UpdateThreadRequest;
use App\Models\Thread;
use App\Services\ThreadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ThreadController extends Controller
{
    public function index(Request $request, ThreadService $threads): View
    {
        $showingArchived = $request->boolean('archived');
        $data = [
            'user' => $request->user(),
            'threads' => $threads->list($showingArchived),
            'showingArchived' => $showingArchived,
            'listHeading' => __($showingArchived ? 'threads.archive' : 'threads.threads'),
            'emptyMessage' => __($showingArchived ? 'threads.empty_archive' : 'threads.empty'),
            'version' => $threads->version(),
        ];

        if ($request->header('X-Fragment') === 'thread-list') {
            return view('components.thread-list', $data);
        }

        return view('threads.index', $data);
    }

    public function store(StoreThreadRequest $request, ThreadService $threads): RedirectResponse
    {
        $thread = $threads->open($request->user(), $request->validated('body'));

        return to_route('threads.show', $thread)->with('status', __('threads.created'));
    }

    public function show(Request $request, Thread $thread, ThreadService $threads): View
    {
        Gate::authorize('view', $thread);

        $thread->load(['user', 'replies.user']);

        if ($request->header('X-Fragment') === 'replies') {
            return view('components.replies', [
                'thread' => $thread,
            ]);
        }

        return view('threads.show', [
            'thread' => $thread,
            'version' => $threads->version(),
        ]);
    }

    public function version(ThreadService $threads): JsonResponse
    {
        return response()->json([
            'version' => $threads->version(),
        ]);
    }

    public function update(UpdateThreadRequest $request, Thread $thread, ThreadService $threads): RedirectResponse
    {
        $threads->rename($request->user(), $thread, $request->validated('title'));

        return to_route('threads.show', $thread)->with('status', __('threads.renamed'));
    }

    public function archive(ArchiveThreadRequest $request, Thread $thread, ThreadService $threads): RedirectResponse
    {
        $threads->archive($request->user(), $thread);

        return to_route('threads.show', $thread)->with('status', __('threads.archived'));
    }

    public function unarchive(ArchiveThreadRequest $request, Thread $thread, ThreadService $threads): RedirectResponse
    {
        $threads->unarchive($request->user(), $thread);

        return to_route('threads.show', $thread)->with('status', __('threads.unarchived'));
    }

    public function destroy(DeleteThreadRequest $request, Thread $thread, ThreadService $threads): RedirectResponse
    {
        $archived = $thread->isArchived();

        $threads->delete($request->user(), $thread);

        return to_route('home', $archived ? ['archived' => 1] : [])
            ->with('status', __('threads.deleted'));
    }
}
