<?php

use App\Http\Controllers\Chat\DestroyConversation;
use App\Http\Controllers\Chat\DestroyMessage;
use App\Http\Controllers\Chat\ListConversations;
use App\Http\Controllers\Chat\ShowConversation;
use App\Http\Controllers\Chat\StoreConversation;
use App\Http\Controllers\Chat\StoreMessage;
use App\Http\Controllers\Chat\UpdateMessage;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\Profile\EditProfile;
use App\Http\Controllers\Profile\ShowProfile;
use App\Http\Controllers\Profile\UpdateProfile;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/feeds', FeedController::class)->name('feeds');

    Route::get('/profile/{user}/edit', EditProfile::class)->name('profile.edit');
    Route::get('/profile/{user}', ShowProfile::class)->name('profile.show');
    Route::patch('/profile/{user}', UpdateProfile::class)->name('profile.update');

    Route::get('/settings', SettingController::class)->name('settings');

    Route::get('/inbox', ListConversations::class)->name('chat.inbox');
    Route::post('/conversations/{conversation}/messages', StoreMessage::class)->name('chat.conversations.messages.store');
    Route::patch('/conversations/{conversation}/messages/{message}', UpdateMessage::class)->name('chat.conversations.messages.update');
    Route::delete('/conversations/{conversation}/messages/{message}', DestroyMessage::class)->name('chat.conversations.messages.destroy');
    Route::post('/conversations', StoreConversation::class)->name('chat.conversations.store');
    Route::delete('/conversations/{conversation}', DestroyConversation::class)->name('chat.conversations.destroy');
    Route::get('/conversations/{conversation}', ShowConversation::class)->name('chat.conversations.show');
});
