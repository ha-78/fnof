<?php

use App\Http\Controllers\ColorPaletteController;
use App\Http\Controllers\DeviceRememberController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReplyController;
use App\Http\Controllers\ThreadController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/colors', ColorPaletteController::class)->name('colors');

Route::middleware('auth')->group(function () {
    Route::get('/home', HomeController::class)->name('home');

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
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.role.update');
    Route::put('/users/{user}/features', [UserController::class, 'updateFeatures'])->name('users.features.update');
});
