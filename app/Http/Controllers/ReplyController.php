<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteReplyRequest;
use App\Http\Requests\StoreReplyRequest;
use App\Models\Reply;
use App\Models\Thread;
use App\Services\ThreadService;
use Illuminate\Http\RedirectResponse;

class ReplyController extends Controller
{
    public function store(StoreReplyRequest $request, Thread $thread, ThreadService $threads): RedirectResponse
    {
        $threads->reply($request->user(), $thread, $request->validated('body'));

        return to_route('threads.show', $thread)->with('status', __('threads.replied'));
    }

    public function destroy(DeleteReplyRequest $request, Thread $thread, Reply $reply, ThreadService $threads): RedirectResponse
    {
        $threads->deleteReply($request->user(), $reply);

        return to_route('threads.show', $thread)->with('status', __('threads.reply_deleted'));
    }
}
