<?php

namespace App\Http\Controllers;

use App\Services\ThreadService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(Request $request, ThreadService $threads): View
    {
        $showingArchived = $request->boolean('archived');

        return view('home', [
            'user' => $request->user(),
            'threads' => $threads->list($showingArchived),
            'showingArchived' => $showingArchived,
            'listHeading' => __($showingArchived ? 'threads.archive' : 'threads.threads'),
            'emptyMessage' => __($showingArchived ? 'threads.empty_archive' : 'threads.empty'),
        ]);
    }
}
