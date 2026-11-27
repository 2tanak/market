<?php

use Illuminate\Support\Facades\Route;
use Modules\Chat\Http\Controllers\ChatController;
use Modules\Chat\Http\Controllers\Api\UsersController as Users;



Route::group(['middleware' => ['auth.admin']], function () {
	Route::get('chat/{user?}', [ChatController::class, 'chat'])->name('chat');
	
	
});