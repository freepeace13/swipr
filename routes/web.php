<?php

use App\Http\Controllers\Chat\ConversationController;
use App\Http\Controllers\Chat\InboxController;
use App\Http\Controllers\Chat\MessageController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/feeds', FeedController::class)->name('feeds');

    Route::get('/profile/{user}/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profile/{user}', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/settings', SettingController::class)->name('settings');

    Route::get('/inbox', InboxController::class)->name('chat.inbox');
    Route::post('/conversations/{conversation}/messages', [MessageController::class, 'store'])->name('chat.conversations.messages.store');
    Route::patch('/conversations/{conversation}/messages/{message}', [MessageController::class, 'update'])->name('chat.conversations.messages.update');
    Route::delete('/conversations/{conversation}/messages/{message}', [MessageController::class, 'destroy'])->name('chat.conversations.messages.destroy');
    Route::post('/conversations', [ConversationController::class, 'store'])->name('chat.conversations.store');
    Route::delete('/conversations/{conversation}', [ConversationController::class, 'destroy'])->name('chat.conversations.destroy');
    Route::get('/conversations/{conversation}', [ConversationController::class, 'show'])->name('chat.conversations.show');
});
