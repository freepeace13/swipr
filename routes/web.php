<?php

use App\Http\Controllers\Chat\InboxController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/feeds', FeedController::class)->name('feeds');
    Route::get('/profile/{user}', ProfileController::class)->name('profile');
    Route::get('/settings', SettingController::class)->name('settings');
    Route::get('/inbox', InboxController::class)->name('chat.inbox');
});
