<?php

use Illuminate\Support\Facades\Route;
use Modules\Chat\Http\Controllers\ChatController;
use Modules\Chat\Http\Controllers\Api\UsersController as Chats;

use Modules\Chat\Http\Controllers\UsersController;

Route::group(['middleware' => ['auth.admin']], function () {
	// Route::get('chat/{user?}', [UsersController::class, 'chat'])->name('chat');
	Route::get('chats', [Chats::class, 'index'])->name('chats');
});