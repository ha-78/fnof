<?php

use App\Http\Controllers\AcceptInviteController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\ColorPaletteController;
use App\Http\Controllers\ContextController;
use App\Http\Controllers\DeviceRememberController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InviteController;
use App\Http\Controllers\ListController;
use App\Http\Controllers\ListItemController;
use App\Http\Controllers\ReplyController;
use App\Http\Controllers\ThreadController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/colors', ColorPaletteController::class)->name('colors');

Route::middleware('throttle:60,1')->group(function () {
    Route::get('/invites/{token}', [AcceptInviteController::class, 'create'])
        ->name('invites.accept')
        ->where('token', '[A-Za-z0-9]{64}');
    Route::post('/invites/{token}', [AcceptInviteController::class, 'store'])
        ->name('invites.accept.store')
        ->where('token', '[A-Za-z0-9]{64}');
});

Route::middleware('auth')->group(function () {
    Route::get('/home', HomeController::class)->name('home');

    Route::get('/lists', [ListController::class, 'index'])->name('lists.index');
    Route::get('/lists/edit', [ListController::class, 'edit'])->name('lists.edit');
    Route::post('/lists', [ListController::class, 'store'])->name('lists.store');
    Route::post('/lists/items', [ListItemController::class, 'store'])->name('lists.items.store');
    Route::delete('/lists/items/{item}', [ListItemController::class, 'destroy'])->name('lists.items.destroy');
    Route::delete('/lists/{list}', [ListController::class, 'destroy'])->name('lists.destroy');

    Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks.index');
    Route::post('/bookmarks', [BookmarkController::class, 'store'])->name('bookmarks.store');
    Route::delete('/bookmarks/{bookmark}', [BookmarkController::class, 'destroy'])->name('bookmarks.destroy');

    Route::get('/threads', [ThreadController::class, 'index'])->name('threads.index');
    Route::get('/threads/version', [ThreadController::class, 'version'])->name('threads.version');
    Route::post('/threads', [ThreadController::class, 'store'])->name('threads.store');
    Route::get('/threads/{thread}', [ThreadController::class, 'show'])->name('threads.show');
    Route::patch('/threads/{thread}', [ThreadController::class, 'update'])->name('threads.update');
    Route::patch('/threads/{thread}/archive', [ThreadController::class, 'archive'])->name('threads.archive');
    Route::patch('/threads/{thread}/unarchive', [ThreadController::class, 'unarchive'])->name('threads.unarchive');
    Route::delete('/threads/{thread}', [ThreadController::class, 'destroy'])->name('threads.destroy');
    Route::post('/threads/{thread}/replies', [ReplyController::class, 'store'])->name('threads.replies.store');
    Route::delete('/threads/{thread}/replies/{reply}', [ReplyController::class, 'destroy'])
        ->name('replies.destroy')
        ->scopeBindings();

    Route::post('/device/theme', [DeviceRememberController::class, 'updateTheme'])->name('device.theme');

    Route::get('/account', [UserController::class, 'account'])->name('account');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/invites', [InviteController::class, 'store'])->name('invites.store');
    Route::delete('/invites/{invite}', [InviteController::class, 'destroy'])->name('invites.destroy');
    Route::patch('/contexts/{context}', [ContextController::class, 'update'])->name('contexts.update');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.role.update');
    Route::put('/users/{user}/features', [UserController::class, 'updateFeatures'])->name('users.features.update');
});
