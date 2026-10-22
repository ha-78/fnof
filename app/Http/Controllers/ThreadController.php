<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArchiveThreadRequest;
use App\Http\Requests\DeleteThreadRequest;
use App\Http\Requests\StoreThreadRequest;
use App\Http\Requests\UpdateThreadRequest;
use App\Models\Thread;
use App\Services\ThreadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ThreadController extends Controller
{
    public function store(StoreThreadRequest $request, ThreadService $threads): RedirectResponse
    {
        $thread = $threads->open($request->user(), $request->validated('body'));

        return to_route('threads.show', $thread)->with('status', __('threads.created'));
    }

    public function show(Thread $thread): View
    {
        Gate::authorize('view', $thread);

        $thread->load(['user', 'replies.user']);

        return view('threads.show', [
            'thread' => $thread,
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
