<?php

use Illuminate\Support\Facades\Route;
use Modules\Chat\Http\Controllers\ChatController;
use Modules\Chat\Http\Controllers\UsersController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
	Route::get('chat/{user?}', [UsersController::class, 'chat'])->name('chat');
    Route::apiResource('chats', ChatController::class)->names('chat');
});
